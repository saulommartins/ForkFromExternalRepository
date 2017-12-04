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
    * Classe de mapeamento da tabela contabilidade.lancamento_retencao
    * Data de Criação: 10/04/2007

    * @author Analista: Gelson W.
    * @author Desenvolvedor: Anderson Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.03.28,uc-02.03.05
*/
/*
$Log$
Revision 1.1  2007/04/30 19:19:03  cako
implementação uc-02.03.28

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  contabilidade.lancamento_retencao
  * Data de Criação: 10/04/2007

  * @author Analista: Gelson W.
  * @author Desenvolvedor: Anderson Konze

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeLancamentoRetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeLancamentoRetencao()
{
    parent::Persistente();
    $this->setTabela("contabilidade.lancamento_retencao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,cod_entidade,exercicio,tipo,sequencia,sequencial');

    $this->AddCampo('cod_lote'          ,'integer',true  ,''    ,true,'TContabilidadeLancamento');
    $this->AddCampo('cod_entidade'      ,'integer',true  ,''    ,true,'TContabilidadeLancamento');
    $this->AddCampo('exercicio'         ,'char'   ,true  ,'04'  ,true,'TContabilidadeLancamento');
    $this->AddCampo('tipo'              ,'char'   ,true  ,'1'   ,true,'TContabilidadeLancamento');
    $this->AddCampo('sequencia'         ,'integer',true  ,''    ,true,'TContabilidadeLancamento');
    $this->AddCampo('cod_ordem'         ,'integer',true  ,''    ,false,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('cod_plano'         ,'integer',true  ,''    ,false,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('exercicio_retencao','char'   ,true  ,'4'   ,false,'TEmpenhoOrdemPagamentoRetencao','exercicio');
    $this->AddCampo('estorno'           ,'boolean',false ,''    ,false,false);
    $this->AddCampo('sequencial'        ,'integer',true ,''     ,true, 'TEmpenhoOrdemPagamentoRetencao');

}

function insereLote(&$inCodLote, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaInsereLote();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inCodLote = $rsRecordSet->getCampo ( 'cod_lote' );

    return $obErro;
}

function montaInsereLote()
{
    $stSql  = " SELECT  \n";
    $stSql .= "      contabilidade.fn_insere_lote( ";
    $stSql .= " '".$this->getDado('exercicio')."' ";
    $stSql .= " ,".$this->getDado('cod_entidade');
    $stSql .= " ,'".$this->getDado('tipo')."' ";
    $stSql .= " ,'".$this->getDado('nom_lote')."' ";
    $stSql .= " ,'".$this->getDado('dt_lote')."' ";
    $stSql .= " ) as cod_lote \n";

    return $stSql ;
}

}
?>
