<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeFundo extends Persistente
{
    public function TContabilidadeFundo() {
        
        parent::Persistente();

        $this->setTabela('contabilidade.fundo');

        $this->setCampoCod('cod_fundo');

        $this->AddCampo('cod_fundo',     'integer', true,  '',  true, true);
        $this->AddCampo('cod_entidade',  'integer', true,  '',  true, true);
        $this->AddCampo('cod_orgao',     'integer', true,  '',  true, true);
        $this->AddCampo('cod_unidade',   'integer', false, '',  true, true);
        $this->AddCampo('exercicio',     'char',    true,  '4', true, true);
        $this->AddCampo('cnpj',          'text',    true,  '',  true, true);
        $this->AddCampo('descricao',     'text',    true,  '',  true, true);
        $this->AddCampo('plano',         'integer', true,  '',  true, true);
        $this->AddCampo('situacao',      'integer', true,  '',  true, true);
        $this->AddCampo('data_extincao', 'date',    true,  '',  true, true);
        $this->AddCampo('contabilidade_centralizada', 'integer', true, '', true, true);
    }

    public function listar(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;

      if (trim($stOrdem)) {
          $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY $stOrdem" : $stOrdem;
      }

      $stSql = $this->montaRecuperaRelacionamentoFundoEntidade().$stCondicao.$stOrdem;
      $this->setDebug( $stSql );
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
    }

    private function montaRecuperaRelacionamentoFundoEntidade()
    {
      return "
        select fu.cod_fundo, 
               fu.cod_entidade,
               cgm.nom_cgm as entidade,
               fu.cod_orgao,
               org.nom_orgao as orgao,
               fu.cod_unidade,
               uni.num_unidade, 
               uni.nom_unidade as unidade, 
               fu.cnpj, 
               fu.descricao, 
               fu.plano, 
               fu.situacao, 
               fu.exercicio
          from contabilidade.fundo fu

          join orcamento.entidade en
            on en.cod_entidade = fu.cod_entidade
           and en.exercicio = fu.exercicio

          join sw_cgm cgm
            on cgm.numcgm = en.numcgm

          join orcamento.orgao org
            on org.num_orgao = fu.cod_orgao
           and org.exercicio = fu.exercicio
         
          left join orcamento.unidade uni 
            on fu.cod_unidade = uni.num_unidade
           and fu.exercicio = uni.exercicio
           and fu.cod_orgao = uni.num_orgao
      ";
    }

    public function extinguirFundo($exercicio, $cod_fundo, $boTransacao)
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;

      $stSql = " UPDATE " . $this->getTabela() . " SET data_extincao = current_date, situacao = 0
                  WHERE exercicio = '".$exercicio."' and cod_fundo = ".$cod_fundo;
      
      $this->setDebug( $stSql );

      $obErro = $obConexao->executaDML( $stSql, $boTransacao );

      return $obErro;
    }
}
