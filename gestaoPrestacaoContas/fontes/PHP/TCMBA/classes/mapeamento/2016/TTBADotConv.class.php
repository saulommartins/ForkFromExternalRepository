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
    * Data de Criação: 17/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBADotConv extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
}

function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDados()
{
    $stSql = "  SELECT 1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , empenho_convenio.num_convenio||empenho_convenio.exercicio AS num_convenio
                       , pre_empenho_despesa.cod_despesa||pre_empenho_despesa.exercicio AS nu_conveniodotacao 
                       , SUBSTRING(REPLACE(conta_despesa.cod_estrutural,'.',''),1,8) AS cd_elemento
                       , LPAD(orcamento.despesa.num_unidade::VARCHAR,2,'0') AS cd_unidadeorcamentaria
                       , pre_empenho_despesa.exercicio AS dt_ano
                       , CASE WHEN SUBSTRING(LPAD(acao.num_acao::VARCHAR,4,'0'),1,1)= '0' THEN 
                                '3'
                            ELSE 
                                SUBSTRING(LPAD(acao.num_acao::VARCHAR,4,'0'),1,1) 
                         END AS tp_projetoatividade
                       , LPAD(acao.num_acao::VARCHAR,4,'0') AS nu_projetoatividade
                       , despesa.cod_recurso AS cd_fonterecurso
                       , LPAD(despesa.cod_funcao::VARCHAR,2,'0') AS cd_funcao
                       , LPAD(despesa.cod_subfuncao::VARCHAR,4,'0') AS cd_subfuncao
                       , LPAD(despesa.cod_programa::VARCHAR,4,'0') AS cd_programa
                       , LPAD(despesa.num_orgao::VARCHAR,4,'0') AS cd_orgao
                       , '".$this->getDado('stExercicio').$this->getDado('inMes')."' AS competencia
                  FROM empenho.empenho_convenio

            INNER JOIN empenho.empenho
                    ON empenho.exercicio    = empenho_convenio.exercicio
                   AND empenho.cod_entidade = empenho_convenio.cod_entidade     
                   AND empenho.cod_empenho  = empenho_convenio.cod_empenho              

            INNER JOIN empenho.pre_empenho
                   ON pre_empenho.exercicio       = empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

            INNER JOIN empenho.pre_empenho_despesa
                   ON pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

            INNER JOIN orcamento.despesa
                    ON despesa.exercicio   = pre_empenho_despesa.exercicio
                   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

            INNER JOIN orcamento.conta_despesa
                    ON conta_despesa.exercicio = despesa.exercicio
                   AND conta_despesa.cod_conta = despesa.cod_conta

            INNER JOIN orcamento.programa_ppa_programa
                    ON programa_ppa_programa.cod_programa = despesa.cod_programa
                   AND programa_ppa_programa.exercicio   = despesa.exercicio

            INNER JOIN ppa.programa
                    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa

            INNER  JOIN orcamento.pao_ppa_acao
                     ON pao_ppa_acao.num_pao = despesa.num_pao
                    AND pao_ppa_acao.exercicio = despesa.exercicio

            INNER JOIN ppa.acao 
                    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
         
            WHERE empenho_convenio.exercicio = '".$this->getDado('stExercicio')."'
              AND empenho.cod_entidade IN ( ".$this->getDado('stEntidades')." )
              AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')" ;
              
    return $stSql;
}

}

?>