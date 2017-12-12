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
* Classe de mapeamento da tabela PESSOAL.FAIXA_DESCONTO
* Data de Criação: 14/12/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHA_PAGAMENTO_FAIXA_DESCONTO.FAIXA_DESCONTO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFaixaDesconto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFaixaDesconto()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.faixa_desconto');

    $this->setCampoCod('cod_faixa');
    $this->setComplementoChave('');

    $this->AddCampo('cod_faixa'            , 'INTEGER'  , true, ''    ,  true, false);
    $this->AddCampo('cod_previdencia'      , 'INTEGER'  , true, ''    ,  true,  true);
    $this->AddCampo('timestamp_previdencia', 'timestamp', true, ''    ,  true,  true);
    $this->AddCampo('valor_inicial'        , 'numeric'  , true, '14.2', false, false);
    $this->AddCampo('valor_final'          , 'numeric'  , true, '14.2', false, false);
    $this->AddCampo('percentual_desconto'  , 'numeric'  , true, '5.2' , false, false);
}

function recuperaLista(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLista($stOrder);
    $stSql .= $stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLista()
{
    $stSql  = "SELECT                                                              \n";
    $stSql .= "   FFD.*                                                            \n";
    $stSql .= "FROM                                                                \n";
    $stSql .= "   folhapagamento.faixa_desconto FFD                                \n";
    $stSql .= "   LEFT JOIN folhapagamento.previdencia_previdencia as FPP          \n";
    $stSql .= "      ON  FPP.cod_previdencia = FFD.cod_previdencia                 \n";
    $stSql .= "      AND FPP.timestamp       = FFD.timestamp_previdencia,          \n";
    $stSql .= "   (SELECT M_FPP.cod_previdencia, max(M_FPP.timestamp) as timestamp \n";
    $stSql .= "    FROM folhapagamento.previdencia_previdencia M_FPP               \n";
    $stSql .= "    GROUP BY M_FPP.cod_previdencia) as MAX_FPP                      \n";
    $stSql .= "WHERE FFD.cod_previdencia       = MAX_FPP.cod_previdencia           \n";
    $stSql .= "AND   FFD.timestamp_previdencia = MAX_FPP.timestamp                 \n";
    if ( $this->getDado('cod_previdencia') )
    $stSql .= "AND   FFD.cod_previdencia       = ".$this->getDado('cod_previdencia')." \n";
    if ( $this->getDado('cod_faixa') )
    $stSql .= "AND   FFD.cod_faixa             = ".$this->getDado('cod_faixa')."       \n";

    return $stSql;
}

}
