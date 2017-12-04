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
    * Classe de mapeamento da tabela CONTABILIDADE.PAGAMENTO
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: jose.eduardo $
    $Date: 2006-09-29 13:58:57 -0300 (Sex, 29 Set 2006) $

    * Casos de uso: uc-02.02.23,uc-02.04.05
*/

/*
$Log$
Revision 1.9  2006/09/29 16:55:18  jose.eduardo
Bug #7060#

Revision 1.8  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadePagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadePagamento()
{
    parent::Persistente();
    $this->setTabela('contabilidade.pagamento');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,sequencia,tipo,cod_lote,cod_entidade');

    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_nota','integer',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',true,'',false,false);
    $this->AddCampo('exercicio_liquidacao','char',true,'04',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql   = " SELECT                                                   \n";
    $stSql  .= "     ccc.cod_plano,                                       \n";
    $stSql  .= "     cpa.exercicio,                                       \n";
    $stSql  .= "     cpc.nom_conta                                        \n";
    $stSql  .= " FROM                                                     \n";
    $stSql  .= "     contabilidade.plano_conta           as cpc,          \n";
    $stSql  .= "     contabilidade.plano_analitica       as cpa,          \n";
    $stSql  .= "     contabilidade.conta_credito         as ccc,          \n";
    $stSql  .= "     contabilidade.valor_lancamento      as cvl,          \n";
    $stSql  .= "     contabilidade.lancamento            as cla,          \n";
    $stSql  .= "     contabilidade.lancamento_empenho    as cle,          \n";
    $stSql  .= "     contabilidade.pagamento             as cpg,          \n";
    $stSql  .= "     empenho.nota_liquidacao_paga        as enp           \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " WHERE                                                    \n";
    $stSql  .= "     cpc.cod_conta            = cpa.cod_conta             \n";
    $stSql  .= " AND cpc.exercicio            = cpa.exercicio             \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND cpa.cod_plano            = ccc.cod_plano             \n";
    $stSql  .= " AND cpa.exercicio            = ccc.exercicio             \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND ccc.cod_lote             = cvl.cod_lote              \n";
    $stSql  .= " AND ccc.tipo                 = cvl.tipo                  \n";
    $stSql  .= " AND ccc.sequencia            = cvl.sequencia             \n";
    $stSql  .= " AND ccc.exercicio            = cvl.exercicio             \n";
    $stSql  .= " AND ccc.tipo_valor           = cvl.tipo_valor            \n";
    $stSql  .= " AND ccc.cod_entidade         = cvl.cod_entidade          \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND cvl.sequencia            = cla.sequencia             \n";
    $stSql  .= " AND cvl.cod_lote             = cla.cod_lote              \n";
    $stSql  .= " AND cvl.tipo                 = cla.tipo                  \n";
    $stSql  .= " AND cvl.exercicio            = cla.exercicio             \n";
    $stSql  .= " AND cvl.cod_entidade         = cla.cod_entidade          \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND cla.cod_lote             = cle.cod_lote              \n";
    $stSql  .= " AND cla.tipo                 = cle.tipo                  \n";
    $stSql  .= " AND cla.sequencia            = cle.sequencia             \n";
    $stSql  .= " AND cla.exercicio            = cle.exercicio             \n";
    $stSql  .= " AND cla.cod_entidade         = cle.cod_entidade          \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND cle.exercicio            = cpg.exercicio             \n";
    $stSql  .= " AND cle.sequencia            = cpg.sequencia             \n";
    $stSql  .= " AND cle.tipo                 = cpg.tipo                  \n";
    $stSql  .= " AND cle.cod_lote             = cpg.cod_lote              \n";
    $stSql  .= " AND cle.cod_entidade         = cpg.cod_entidade          \n";
    $stSql  .= "                                                          \n";
    $stSql  .= " AND cpg.cod_entidade         = enp.cod_entidade          \n";
    $stSql  .= " AND cpg.cod_nota             = enp.cod_nota              \n";
    $stSql  .= " AND cpg.exercicio_liquidacao = enp.exercicio             \n";
    $stSql  .= " AND cpg.timestamp            = enp.timestamp             \n";
    $stSql  .= "                                                          \n";
//    $stSql  .= " AND cpg.sequencia            = 2                         \n";
    $stSql  .= " AND cle.estorno              = false                     \n";

    return $stSql;
}
}
