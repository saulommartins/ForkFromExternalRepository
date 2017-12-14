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
    * Classe de mapeamento da tabela compras.fornecedor_atividade
    * Data de Criação: 30/06/2006

    * @author Analista: Cleissom Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 25419 $
    $Name$
    $Author: bruce $
    $Date: 2007-09-12 17:03:52 -0300 (Qua, 12 Set 2007) $

    * Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.5  2007/09/12 20:03:52  bruce
Ticket#8195#

Revision 1.4  2007/08/07 15:41:59  bruce
Bug#9820#

Revision 1.3  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.2  2006/09/29 17:35:31  fernando
implementado a alteração do UC-03.04.03

Revision 1.1  2006/09/21 15:06:28  fernando
UC-03.04.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.fornecedor_conta
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasFornecedorConta extends Persistente
{
function TComprasFornecedorConta()
{
    parent::Persistente();
    $this->setTabela("compras.fornecedor_conta");

    $this->setCampoCod('num_conta');
    $this->setComplementoChave('cod_banco, cod_agencia, cgm_fornecedor');

    $this->AddCampo('num_conta','varchar',true,true,'20','','','');
    $this->AddCampo('padrao','BOOLEAN',false,'','','',false);
    $this->AddCampo('cgm_fornecedor','INTEGER',true,true,'',true,'','','TComprasFornecedor');
    $this->AddCampo('cod_banco','INTEGER',true,true,'',true,'','','','TMONAgencia');
    $this->AddCampo('cod_agencia','INTEGER',true,true,'','',true,'','','TMONAgencia');

}

function recuperaListaFornecedorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaFornecedorConta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaListaFornecedorConta()
{
    $stSql  ="SELECT                                      \n";
    $stSql .="     fc.cgm_fornecedor                      \n";
    $stSql .="    ,ma.num_agencia                         \n";
    $stSql .="    ,fc.num_conta                           \n";
    $stSql .="    ,mb.num_banco                           \n";
    $stSql .="    ,fc.cod_banco                           \n";
    $stSql .="    ,fc.cod_agencia                         \n";
    $stSql .="    ,fc.padrao                              \n";
    $stSql .="    ,mb.nom_banco                           \n";
    $stSql .="    ,ma.nom_agencia                         \n";
    $stSql .="FROM                                        \n";
    $stSql .="     compras.fornecedor_conta as fc         \n";
    $stSql .="    ,monetario.agencia as ma                \n";
    $stSql .="    ,monetario.banco as mb                  \n";
    $stSql .="WHERE                                       \n";
    $stSql .="        fc.cod_agencia = ma.cod_agencia     \n";
    $stSql .="    AND fc.cod_banco = ma.cod_banco         \n";
    $stSql .="    AND ma.cod_banco = mb.cod_banco         \n";
    if ($this->getDado('cgm_fornecedor') )
        $stSql .=" AND fc.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')." \n";

    return $stSql;

}
}
