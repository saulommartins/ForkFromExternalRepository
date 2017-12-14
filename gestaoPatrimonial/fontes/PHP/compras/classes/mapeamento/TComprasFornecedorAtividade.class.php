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

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19454 $
    $Name$
    $Author: tonismar $
    $Date: 2007-01-19 09:04:49 -0200 (Sex, 19 Jan 2007) $

    * Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.8  2007/01/19 11:02:24  tonismar
bug #8109

Revision 1.7  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.6  2006/09/29 17:35:31  fernando
implementado a alteração do UC-03.04.03

Revision 1.5  2006/09/21 15:06:18  fernando
UC-03.04.03

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.fornecedor_atividade
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasFornecedorAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasFornecedorAtividade()
{
    parent::Persistente();
    $this->setTabela("compras.fornecedor_atividade");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_fornecedor,cod_atividade');

    $this->AddCampo('cgm_fornecedor','INTEGER',true,'',true,'TComprasFornecedor');
    $this->AddCampo('cod_atividade','INTEGER',true,'',true,'TCEMAtividade');

}

function recuperaAtividadeFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeFornecedor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}
function montaRecuperaAtividadeFornecedor()
{
$stSql  ="SELECT                                       \n";
$stSql .="     fa.cgm_fornecedor                      \n";
$stSql .="    ,fa.cod_atividade                       \n";
$stSql .="    ,ea.nom_atividade                       \n";
$stSql .="    ,ea.cod_estrutural                      \n";
$stSql .="FROM                                        \n";
$stSql .="     compras.fornecedor_atividade as fa     \n";
$stSql .="    ,economico.atividade as ea              \n";
$stSql .="WHERE                                       \n";
$stSql .="    fa.cod_atividade = ea.cod_atividade     \n";
if ($this->getDado('cgm_fornecedor'))
    $stSql .="AND fa.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')." \n";
return $stSql;

}

}
