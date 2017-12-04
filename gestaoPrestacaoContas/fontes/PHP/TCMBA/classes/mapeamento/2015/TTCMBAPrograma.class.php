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
    * Data de Criação: 13/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/06/22 22:50:29  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrograma.class.php" );

/**
  *
  * Data de Criação: 13/06/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCMBAPrograma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMBAPrograma()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
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
    $stSql = " SELECT   programa.exercicio AS ano
                       , ppa.num_programa
                       , 0 AS reservado_tcm
                       , programa.descricao
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , pd.objetivo
                       , COALESCE((SELECT SUM(vl_original) FROM orcamento.despesa AS des WHERE des.exercicio = programa.exercicio AND des.cod_programa = programa.cod_programa),0.00) AS valor
                       , 1 AS tipo_registro

                  FROM orcamento.programa

            INNER JOIN orcamento.programa_ppa_programa
                    ON programa_ppa_programa.exercicio = programa.exercicio
                   AND programa_ppa_programa.cod_programa_ppa = programa.cod_programa

            INNER JOIN ppa.programa AS ppa
                    ON ppa.cod_programa = programa_ppa_programa.cod_programa_ppa

            INNER JOIN ppa.programa_dados AS pd
                    ON pd.cod_programa = ppa.cod_programa
                   AND pd.timestamp_programa_dados = (SELECT MAX(timestamp_programa_dados) FROM ppa.programa_dados WHERE programa_dados.cod_programa = pd.cod_programa)

                 WHERE programa.exercicio = '".$this->getDado('exercicio')."'
              GROUP BY ano
                     , programa.cod_programa
                     , reservado_tcm
                     , descricao
                     , unidade_gestora
                     , objetivo
                     , valor
                     , ppa.num_programa
              ORDER BY programa.exercicio, programa.cod_programa
            ";

    return $stSql;
}

}
