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
    * Classe de mapeamento da tabela FN_BALANCETE_DESPESA
    * Data de Criação: 05/03/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.9  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoTCERSBalanceteDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoTCERSBalanceteDespesa()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_balancete_despesa');

    $this->AddCampo('num_orgao'                 ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_unidade'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_funcao'                ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_subfuncao'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_programa'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_subprograma'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_pao'                   ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_subelemento'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_recurso'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('saldo_inicial'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('atualizacao'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('credito_suplementar'       ,'varchar',false,''    ,false,false);
    $this->AddCampo('credito_especial'          ,'varchar',false,''    ,false,false);
    $this->AddCampo('credito_extraordinario'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('reducoes'                  ,'varchar',false,''    ,false,false);
    $this->AddCampo('suplementacao'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('reducao'                   ,'varchar',false,''    ,false,false);
    $this->AddCampo('empenho_per'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('anulado_per'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('liquidado_per'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('pago_per'                  ,'varchar',false,''    ,false,false);
    $this->AddCampo('total_creditos'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor_liquidado'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('recomposicao'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('previsao'                  ,'varchar',false,''    ,false,false);

}

function montaRecuperaDadosExportacao()
{
    $stSql   = " SELECT                                                                       \n";
    $stSql  .= "    cod_despesa                  ,                                            \n";
    $stSql  .= "    num_orgao                    ,                                            \n";
    $stSql  .= "    num_unidade                  ,                                            \n";
    $stSql  .= "    cod_funcao                          ,                                     \n";
    $stSql  .= "    cod_subfuncao                ,                                            \n";
    $stSql  .= "    cod_programa                 ,                                            \n";
    $stSql  .= "    cod_subprograma              ,                                            \n";
    $stSql  .= "    num_pao                      ,                                            \n";
    $stSql  .= "    cod_subelemento              ,                                            \n";
    $stSql  .= "    cod_recurso                  ,                                            \n";
    $stSql  .= "    saldo_inicial                ,                                            \n";
    $stSql  .= "    atualizacao                  ,                                            \n";
    $stSql  .= "    credito_suplementar          ,                                            \n";
    $stSql  .= "    credito_especial             ,                                            \n";
    $stSql  .= "    credito_extraordinario       ,                                            \n";
    $stSql  .= "    reducoes                     ,                                            \n";
    $stSql  .= "    suplementacao                ,                                            \n";
    $stSql  .= "    reducao                      ,                                            \n";
    $stSql  .= "    (empenho_per - anulado_per) as vl_empenhado                  ,                                            \n";
    $stSql  .= "    liquidado_per                ,                                            \n";
    $stSql  .= "    pago_per                     ,                                            \n";
    $stSql  .= "    valor_liquidado              ,                                            \n";
    $stSql  .= "    recomposicao                 ,                                            \n";
    $stSql  .= "    previsao                                                                  \n";
    $stSql  .= "FROM                                                                             \n";
    $stSql  .= " ".$this->getTabela()."( '".$this->getDado("stExercicio")."',
                                        'AND OD.cod_entidade IN (".$this->getDado("stCodEntidades")  .")',
                                        '".$this->getDado("dtInicial")       ."',
                                        '".$this->getDado("dtFinal")         ."') AS tabela(    \n";
    $stSql  .= "cod_despesa                  integer,                                            \n";
    $stSql  .= "num_orgao                    integer,                                            \n";
    $stSql  .= "num_unidade                  integer,                                            \n";
    $stSql  .= "cod_funcao                   integer,                                            \n";
    $stSql  .= "cod_subfuncao                integer,                                            \n";
    $stSql  .= "cod_programa                 integer,                                            \n";
    $stSql  .= "cod_subprograma              integer,                                            \n";
    $stSql  .= "num_pao                      integer,                                            \n";
    $stSql  .= "cod_subelemento              integer,                                            \n";
    $stSql  .= "cod_recurso                  integer,                                            \n";
    $stSql  .= "saldo_inicial                numeric,                                            \n";
    $stSql  .= "atualizacao                  integer,                                            \n";
    $stSql  .= "credito_suplementar          numeric,                                            \n";
    $stSql  .= "credito_especial             numeric,                                            \n";
    $stSql  .= "credito_extraordinario       numeric,                                            \n";
    $stSql  .= "reducoes                     numeric,                                            \n";
    $stSql  .= "suplementacao                numeric,                                            \n";
    $stSql  .= "reducao                      numeric,                                            \n";
    $stSql  .= "empenho_per                  numeric,                                            \n";
    $stSql  .= "anulado_per                  numeric,                                            \n";
    $stSql  .= "liquidado_per                numeric,                                            \n";
    $stSql  .= "pago_per                     numeric,                                            \n";
    $stSql  .= "total_creditos               numeric,                                            \n";
    $stSql  .= "valor_liquidado              numeric,                                            \n";
    $stSql  .= "recomposicao                 numeric,                                            \n";
    $stSql  .= "previsao                     numeric                                             \n";
    $stSql  .= "        )                                                                        \n";

    return $stSql;
}

/**
    * Executa funcao fn_exportacao_liquidacao no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
