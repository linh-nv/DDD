# DDD Architecture Check

Verify that the codebase respects the DDD layering rules of the TestCenter project.

## Rules to enforce

### 1. Domain layer purity
The `src/Domain/` directory must have **zero** imports from Laravel or Eloquent:
```bash
grep -r "use Illuminate\\" src/Domain/ && echo "VIOLATION FOUND" || echo "OK — domain is pure"
grep -r "use App\\" src/Domain/ && echo "VIOLATION FOUND" || echo "OK"
```

### 2. Validation in VOs, not handlers/services
Value object constructors must throw on invalid input. Scan for validation logic outside of `__construct`:
- Look for `if (empty(...))` or `throw new InvalidArgumentException` outside VO constructors

### 3. Repositories return domain objects
Infrastructure repositories must never return Eloquent models from public methods:
```bash
grep -r "return \$" src/Infrastructure/ | grep -v "toDomain\|mapper\|buildCategory\|getPairs"
```

### 4. Events released in handler, not domain
Domain aggregates record events via `$this->recordEvent(...)`.
Publishing must only happen in `SubmitExamHandler` via `$this->publisher->publish(...)`.

### 5. No business logic in handlers
`SubmitExamHandler` must not contain `if` branches that enforce business rules — those belong in the domain.

## Report format

For each rule: ✅ OK or ❌ VIOLATION with file:line reference.

Then summarize: how many violations found and which layer they are in.
