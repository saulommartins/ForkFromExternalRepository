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
Revision 1.8  2007/03/22 19:34:20  tonismar
bug #8696 #8695

Revision 1.7  2006/10/10 14:52:45  larocca
BUG #7153#

Revision 1.6  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:32  diego

*/

include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoItens.class.php"                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                  );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php"                   );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoRequisicaoItem.class.php");
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoRequisicaoItemValor.class.php");

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoRequisicaoItem
{

    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;

    /**
        * @access Private
        * @var Object
    */
    public $obRAlmoxarifadoEstoqueItem;

    /**
        * @access Private
        * @var Numeric
    */

    public $nmQuantidade;

    /**
         * @access Public
         * @return Numeric
     */

   public function setQuantidade($valor) { $this->nmQuantidade = $valor; }

    /**
        *@access Public
    */
   public function setValoresAtributos($valor) { $this->arValoresAtributos = $valor; }

    /**
         * @access Public
         * @return Numeric
     */

    public function getQuantidade() { return $this->nmQuantidade; }

    /**
         * @access Public
         * @return array
     */
    public function getValoresAtributos() { return $this->arValoresAtributos; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoRequisicaoItem(&$obRAlmoxarifadoRequisicao)
    {
        $this->obRAlmoxarifadoEstoqueItem = new RAlmoxarifadoEstoqueItem();
        $this->roAlmoxarifadoRequisicao = &$obRAlmoxarifadoRequisicao;
        $this->obTransacao  = new Transacao ;
    }

    public function listar(&$rsRecordSet, $stOrder = '', $boTransacao = '')
    {
        $stFiltro = "";
        if ($this->roAlmoxarifadoRequisicao->getCodigo()) {
           $stFiltro .= " WHERE cod_requisicao = ". $this->roAlmoxarifadoRequisicao->getCodigo();
        }
        if ($this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo()) {
           $stFiltro .= " AND cod_almoxarifado = ". $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo();
        }
        if ($this->roAlmoxarifadoRequisicao->getExercicio()) {
           $stFiltro .= " AND exercicio = '". $this->roAlmoxarifadoRequisicao->getExercicio()."'";
        }
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();
        $obErro = $obTAlmoxarifadoRequisicaoItens->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function consultar($boTransacao = "")
    {
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_requisicao" , $this->roAlmoxarifadoRequisicao->getCodigo() );
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_almoxarifado", $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado( "exercicio", $this->roAlmoxarifadoRequisicao->getExercicio());
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_item" , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_marca", $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_centro", $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obErro = $obTAlmoxarifadoRequisicaoItens->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $this->setQuantidade( $rsRecordSet->getCampo("quantidade") );
           $obErro = $this->obRAlmoxarifadoEstoqueItem->consultar($boTransacao);
        }

        return $obErro;
    }

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
            $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();
            $obTAlmoxarifadoRequisicaoItens->setDado("cod_requisicao"      , $this->roAlmoxarifadoRequisicao->getCodigo() );
            $obTAlmoxarifadoRequisicaoItens->setDado("cod_almoxarifado"    , $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo() );
            $obTAlmoxarifadoRequisicaoItens->setDado("exercicio"           , $this->roAlmoxarifadoRequisicao->getExercicio());
            $obTAlmoxarifadoRequisicaoItens->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
            $obTAlmoxarifadoRequisicaoItens->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
            $obTAlmoxarifadoRequisicaoItens->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
            $obTAlmoxarifadoRequisicaoItens->setDado("quantidade"          , $this->getQuantidade() );
            $obErro = $obTAlmoxarifadoRequisicaoItens->inclusao( $boTransacao );
        }

        if ( $this->getValoresAtributos() ) {
            foreach ($this->arValoresAtributos as $valor_atributo) {
                $obTAlmoxarifadoAtributoRequisicaoItem = new TAlmoxarifadoAtributoRequisicaoItem;
                $obTAlmoxarifadoAtributoRequisicaoItem->obTAlmoxarifadoRequisicaoItens = &$obTAlmoxarifadoRequisicaoItens;

                $obTAlmoxarifadoAtributoRequisicaoItem->setDado( "quantidade"      , $valor_atributo['quantidade'] );
                $obErro = $obTAlmoxarifadoAtributoRequisicaoItem->inclusao( $boTransacao );

                foreach ($valor_atributo['atributo'] as $atributo) {
                    $obTAlmoxarifadoAtributoRequisicaoItemValor = new TAlmoxarifadoAtributoRequisicaoItemValor;
                    $obTAlmoxarifadoAtributoRequisicaoItemValor->obTAlmoxarifadoAtributoRequisicaoItem = &$obTAlmoxarifadoAtributoRequisicaoItem;
                    $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_modulo"  , "29" );
                    $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_cadastro", "2" );
                    $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_atributo", $atributo["cod_atributo"] );
                    $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "valor"       , $atributo["valor"] );
                    $obErro = $obTAlmoxarifadoAtributoRequisicaoItemValor->inclusao( $boTransacao );
                    unset( $obTAlmoxarifadoAtributoRequisicaoItemValor );
                }

                unset($obTAlmoxarifadoAtributoRequisicaoItem);
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoRequisicaoItens);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function alterar($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();
            if ( !$obErro->ocorreu() ) {
                 $obTAlmoxarifadoRequisicaoItens->setDado("cod_requisicao"      , $this->roAlmoxarifadoRequisicao->getCodigo() );
                 $obTAlmoxarifadoRequisicaoItens->setDado("cod_almoxarifado"    , $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo() );
                 $obTAlmoxarifadoRequisicaoItens->setDado("exercicio"           , $this->roAlmoxarifadoRequisicao->getExercicio());
                 $obTAlmoxarifadoRequisicaoItens->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
                 $obTAlmoxarifadoRequisicaoItens->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
                 $obTAlmoxarifadoRequisicaoItens->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
                 $obTAlmoxarifadoRequisicaoItens->setDado("quantidade"          , $this->getQuantidade());
                 $obErro = $obTAlmoxarifadoRequisicao->alteracao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoRequisicaoItens);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function excluir($boTransacao="")
    {
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_requisicao"         , $this->roAlmoxarifadoRequisicao->getCodigo()           );
        $obTAlmoxarifadoRequisicaoItens->setDado( "exercicio"              , $this->roAlmoxarifadoRequisicao->getExercicio()        );
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_almoxarifado"       , $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo() );
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_item"               , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_centro"             , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado( "cod_marca"              , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obErro = $obTAlmoxarifadoRequisicaoItens->exclusao( $boTransacao );

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function retornaSaldoRequisitado(&$inSaldo, $boTransacao="")
    {
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens;
        $stFiltro = "";
        $obTAlmoxarifadoRequisicaoItens->setDado("exercicio", $this->roAlmoxarifadoRequisicao->getExercicio() );
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_almoxarifado", $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_requisicao", $this->roAlmoxarifadoRequisicao->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_item", $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_centro", $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_marca", $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obErro = $obTAlmoxarifadoRequisicaoItens->recuperaSaldoRequisitado( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );
        $inSaldo = $rsRecordSet->getCampo( 'saldo_requisitado' );

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function retornaSaldoAtendido(&$inSaldo, $boTransacao="")
    {
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens;
        $stFiltro = "";
        $obTAlmoxarifadoRequisicaoItens->setDado("exercicio", $this->roAlmoxarifadoRequisicao->getExercicio() );
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_almoxarifado", $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_requisicao", $this->roAlmoxarifadoRequisicao->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_item", $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_centro", $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_marca", $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obErro = $obTAlmoxarifadoRequisicaoItens->recuperaSaldoAtendido( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );
        $inSaldo = abs($rsRecordSet->getCampo( 'saldo_atendido' ));

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function retornaSaldoDevolvido(&$inSaldo, $boTransacao="")
    {
        $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens;
        $stFiltro = "";
        $obTAlmoxarifadoRequisicaoItens->setDado("exercicio", $this->roAlmoxarifadoRequisicao->getExercicio() );
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_almoxarifado", $this->roAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_requisicao", $this->roAlmoxarifadoRequisicao->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_item", $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_centro", $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoRequisicaoItens->setDado("cod_marca", $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obErro = $obTAlmoxarifadoRequisicaoItens->recuperaSaldoDevolvido( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );
        $inSaldo = abs($rsRecordSet->getCampo( 'saldo_devolvido' ));

        return $obErro;
    }
}
