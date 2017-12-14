<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Extensão da Classe de mapeamento
    * Data de Criação: 26/01/2015

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOArquivoOrcamentoDSP extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOArquivoOrcamentoDSP()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaDSP10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDSP10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaDSP10()
{
    $stSql  = " SELECT DISTINCT 10::INTEGER AS tipo_registro
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '9999'
                      ELSE LPAD(programa.num_programa::VARCHAR, 4, '0')::VARCHAR
                 END AS cod_programa
               , unidade_responsavel.num_orgao
               , unidade_responsavel.num_unidade
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '99'
                      ELSE LPAD(despesa.cod_funcao::VARCHAR, 2, '0')::VARCHAR
                 END AS cod_funcao
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '999'
                      ELSE LPAD(despesa.cod_subfuncao::VARCHAR, 3, '0')::VARCHAR
                 END AS cod_subfuncao
               , SUBSTR(acao.num_acao::VARCHAR,1,1) AS natureza_acao
               , SUBSTR(acao.num_acao::VARCHAR,2,3) AS num_proj_atividade
               , SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) AS elemento_despesa     
               , despesa.vl_original AS vl_total_recurso
               , despesa.num_pao::VARCHAR AS num_pao
               , '' AS numero_sequencial
               , '' AS branco
            FROM orcamento.despesa
            JOIN tcmgo.unidade_responsavel
              ON unidade_responsavel.num_unidade=despesa.num_unidade
            JOIN orcamento.conta_despesa
              ON conta_despesa.cod_conta = despesa.cod_conta
             AND conta_despesa.exercicio = despesa.exercicio
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.cod_entidade = despesa.cod_entidade
             AND configuracao_entidade.exercicio = despesa.exercicio
            JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.cod_programa = despesa.cod_programa
             AND programa_ppa_programa.exercicio   = despesa.exercicio
            JOIN ppa.programa
              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            JOIN orcamento.pao_ppa_acao
              ON pao_ppa_acao.num_pao = despesa.num_pao
             AND pao_ppa_acao.exercicio = despesa.exercicio
            JOIN ppa.acao
              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
           WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
             AND despesa.cod_entidade IN ('".$this->getDado('cod_entidade')."')
             AND despesa.vl_original > 0.00
            order by cod_programa
       ";

    return $stSql;
}

public function recuperaDSP11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDSP11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaDSP11()
{
    $stSql  = "
 SELECT DISTINCT 11::INTEGER AS tipo_registro
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '9999'
                      ELSE LPAD(programa.num_programa::VARCHAR, 4, '0')::VARCHAR
                 END AS cod_programa
               , unidade_responsavel.num_orgao
               , unidade_responsavel.num_unidade
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '99'
                      ELSE LPAD(despesa.cod_funcao::VARCHAR, 2, '0')::VARCHAR
                 END AS cod_funcao
               , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9' THEN '999'
                      ELSE LPAD(despesa.cod_subfuncao::VARCHAR, 3, '0')::VARCHAR
                 END AS cod_subfuncao
               , SUBSTR(acao.num_acao::VARCHAR,1,1) AS natureza_acao
               , SUBSTR(acao.num_acao::VARCHAR,2,3) AS num_proj_atividade
               , SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) AS elemento_despesa     
               , despesa.cod_recurso AS cod_fonte_recurso
               , despesa.vl_original AS vl_fonte
               , despesa.num_pao::VARCHAR AS num_pao
               , '' AS numero_sequencial
            FROM orcamento.despesa
            JOIN tcmgo.unidade_responsavel
              ON unidade_responsavel.num_unidade=despesa.num_unidade
            JOIN orcamento.conta_despesa
              ON conta_despesa.cod_conta = despesa.cod_conta
             AND conta_despesa.exercicio = despesa.exercicio
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.cod_entidade = despesa.cod_entidade
             AND configuracao_entidade.exercicio = despesa.exercicio
            JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.cod_programa = despesa.cod_programa
             AND programa_ppa_programa.exercicio   = despesa.exercicio
            JOIN ppa.programa
              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            JOIN orcamento.pao_ppa_acao
              ON pao_ppa_acao.num_pao = despesa.num_pao
             AND pao_ppa_acao.exercicio = despesa.exercicio
            JOIN ppa.acao
              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
           WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
             AND despesa.cod_entidade IN ('".$this->getDado('cod_entidade')."')
             AND despesa.vl_original > 0.00
         order by cod_programa
       ";

    return $stSql;
}
}
