package com.projeto.academico.service;

import com.projeto.academico.model.Aluno;
import com.projeto.academico.repository.AlunoRepository;
import org.springframework.stereotype.Service;
import java.util.List;
import java.util.Optional;

@Service
public class AlunoService {

    private final AlunoRepository repository;

    public AlunoService(AlunoRepository repository) {
        this.repository = repository;
    }

    public List<Aluno> listarTodos() {
        return repository.findAll();
    }

    public Aluno salvar(Aluno aluno) {
        return repository.save(aluno);
    }

    public Aluno buscarPorId(Long id) {
        Optional<Aluno> aluno = repository.findById(id);
        return aluno.orElseThrow(() -> new RuntimeException("Aluno não encontrado com o ID: " + id));
    }

    public Aluno atualizar(Long id, Aluno alunoAtualizado) {
        Aluno alunoExistente = buscarPorId(id);
        alunoExistente.setNome(alunoAtualizado.getNome());
        alunoExistente.setEmail(alunoAtualizado.getEmail());
        alunoExistente.setMatricula(alunoAtualizado.getMatricula());
        return repository.save(alunoExistente);
    }

    public void deletar(Long id) {
        Aluno aluno = buscarPorId(id);
        repository.delete(aluno);
    }
}