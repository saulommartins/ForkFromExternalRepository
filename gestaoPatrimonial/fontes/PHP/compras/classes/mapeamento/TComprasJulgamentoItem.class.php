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
    * Classe de mapeamento da tabela compras.julgamento_item
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21759 $
    $Name$
    $Author: bruce $
    $Date: 2007-04-11 17:09:59 -0300 (Qua, 11 Abr 2007) $

    * Casos de uso: uc-03.04.06
                    uc-03.05.20
                    uc-03.05.26
*/

/*
$Log$
Revision 1.10  2007/04/11 20:09:59  bruce
alterei para aparecer na adjudicação somente o fornecedor que venceu pra cada item ou lote.

Revision 1.9  2007/04/10 20:19:50  bruce
Bug #9039#

Revision 1.8  2006/12/19 12:46:18  bruce
colocação do UC de  julgamento de proposta

Revision 1.7  2006/11/30 16:43:29  bruce
*** empty log message ***

Revision 1.6  2006/11/24 16:58:17  andre.almeida
Adicionada uma consulta: recuperaClassificacaoItens

Revision 1.5  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.julgamento_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasJulgamentoItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasJulgamentoItem()
{
    parent::Persistente();
    $this->setTabela("compras.julgamento_item");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_cotacao,cod_item,cgm_fornecedor');

    $this->AddCampo('exercicio','CHAR',false,true,'4',true,'TComprasJulgamento');
    $this->AddCampo('cod_cotacao','INTEGER',true,true,'',true,'TComprasJulgamento');
    $this->AddCampo('cod_item','INTEGER',true,true,'',true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('cgm_fornecedor','INTEGER',true,true,'',true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('ordem','INTEGER',false,true,'',false,false);
    $this->AddCampo('justificativa','varchar',false,true,'200',false,false);
    $this->AddCampo('lote','INTEGER',false,true,'',true,'TComprasCotacaoFornecedorItem');

}

function recuperaClassificacaoItens(&$rsRecordSet, $stFiltro = '')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $this->getDado('exercicio') != "" )
        $stFiltro  .= " AND julgamento_item.exercicio = '".$this->getDado('exercicio')."'            \n";
    if( $this->getDado('cod_cotacao') != "" )
        $stFiltro .= " AND julgamento_item.cod_cotacao = ".$this->getDado('cod_cotacao')."         \n";
    if( $this->getDado('cod_item') != "" )
        $stFiltro .= " AND julgamento_item.cod_item = ".$this->getDado('cod_item')."               \n";
    if( $this->getDado('cgm_fornecedor') != "" )
        $stFiltro .= " AND julgamento_item.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."   \n";

    if( $stFiltro )
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro)-4);

    $stOrder = "order by julgamento_item.ordem \n";

    $stSql = $this->montaRecuperaClassificacaoItens().$stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaClassificacaoItens()
{
    $stSql  = "    select julgamento_item.exercicio                                                            \n";
    $stSql .= "         , julgamento_item.cod_cotacao                                                          \n";
    $stSql .= "         , julgamento_item.lote                                                                 \n";
    $stSql .= "         , julgamento_item.cod_item                                                             \n";
    $stSql .= "         , julgamento_item.ordem                                                                \n";
    $stSql .= "         , julgamento_item.cgm_fornecedor                                                       \n";
    $stSql .= "         , sw_cgm.nom_cgm                                                                       \n";
    $stSql .= "         , publico.fn_numeric_br(cotacao_fornecedor_item.vl_cotacao) as vl_cotacao              \n";
    $stSql .= "         , case when sw_cgm_pessoa_fisica.numcgm   is not null then sw_cgm_pessoa_fisica.cpf    \n";
    $stSql .= "                when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj \n";
    $stSql .= "                else null                                                                       \n";
    $stSql .= "           end as cnpj_cpf                                                                      \n";
    $stSql .= "      from compras.julgamento_item                                                              \n";
    $stSql .= "      join compras.cotacao_fornecedor_item                                                      \n";
    $stSql .= "        on cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio                   \n";
    $stSql .= "       and cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao                 \n";
    $stSql .= "       and cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item                    \n";
    $stSql .= "       and cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor              \n";
    $stSql .= "       and cotacao_fornecedor_item.lote           = julgamento_item.lote                        \n";
    $stSql .= "      join sw_cgm                                                                               \n";
    $stSql .= "        on sw_cgm.numcgm = julgamento_item.cgm_fornecedor                                       \n";
    $stSql .= " left join sw_cgm_pessoa_fisica                                                                 \n";
    $stSql .= "        on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                          \n";
    $stSql .= " left join sw_cgm_pessoa_juridica                                                               \n";
    $stSql .= "        on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm                                        \n";

    return $stSql;
}

}
