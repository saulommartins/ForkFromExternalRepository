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
    * Classe de Regra de Requisição
    * Data de Criação   : 18/11/2005

    * @author Analista: Diego Victoria Barbosa
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.11
*/

/*
$Log$
Revision 1.4  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:31  diego

*/

include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoRequisicao.class.php"         );
//include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoAnulacao.class.php"         );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoLancamentoRequisicaoItem extends RAlmoxarifadoLancamentoItem
{

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function incluir($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = parent::incluir($boTransacao);
            if ( !$obErro->ocorreu() ) {
               $obTAlmoxarifadoLancamentoRequisicao = new TAlmoxarifadoLancamentoRequisicao();
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_lancamento"      , $this->getCodigo() );
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
               $obTAlmoxarifadoLancamentoRequisicao->setDado("exercicio"           , $this->roAlmoxarifadoLancamento->obRAlmoxarifadoRequisicao->getExercicio());
               $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_requisicao",       $this->roAlmoxarifadoLancamento->obRAlmoxarifadoRequisicao->getCodigo());
               $obErro = $obTAlmoxarifadoLancamentoRequisicao->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoLancamentoRequisicao);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function retornaSaldoAtendido(&$inSaldo, $boTransacao="")
    {
        $obTAlmoxarifadoLancamentoRequisicao = new TAlmoxarifadoLancamentoRequisicao;
        $stFiltro = "";

        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_lancamento"  , $this->getCodigo() );
        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_item"        , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_marca"       , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_almoxarifado", $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_centro"      , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
        $obTAlmoxarifadoLancamentoRequisicao->setDado("exercicio"       , $this->roAlmoxarifadoLancamento->obRAlmoxarifadoRequisicao->getExercicio());
        $obTAlmoxarifadoLancamentoRequisicao->setDado("cod_requisicao"  , $this->roAlmoxarifadoLancamento->obRAlmoxarifadoRequisicao->getCodigo());
        $obErro = $obTAlmoxarifadoLancamentoRequisicao->recuperaSaldoAtendido( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );
        $inSaldo = $rsRecordSet->getCampo( 'saldo_atendido' );

        return $obErro;
    }
}
