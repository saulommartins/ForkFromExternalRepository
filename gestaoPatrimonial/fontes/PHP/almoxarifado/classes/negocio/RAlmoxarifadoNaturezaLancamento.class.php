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
Revision 1.5  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:32  diego

*/

include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php"                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoNatureza.class.php"                  );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoLancamentoItem.class.php"               );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php"               );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoNaturezaLancamento
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
    public $obRAlmoxarifadoNatureza;

    /**
        * @access Private
        * @var Object
    */
    public $obRAlmoxarifadoAlmoxarife;

    /**
        * @access Private
        * @var String
    */

    public $stExercicio;

    /**
        * @access Private
        * @var Integer
    */

    public $inNumero;

    /**
        * @access Private
        * @var Integer
    */

    public $roUltimoLancamentoItem;

    /**
        * @access Private
        * @var Array
    */

    public $arRAlmoxarifadoLancamentoItem;

    /**
         * @access Public
         * @param String
     */

   public function setExercicio($valor) { $this->stExercicio = $valor; }

    /**
         * @access Public
         * @param Integer
     */

   public function setNumero($valor) { $this->inNumero= $valor; }

    /**
         * @access Public
         * @return Integer
     */

    public function getNumero() { return $this->inNumero; }

    /**
         * @access Public
         * @return String
     */

    public function getExercicio() { return $this->stExercicio; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoNaturezaLancamento()
    {
        $this->obRAlmoxarifadoNatureza = new RAlmoxarifadoNatureza();
        $this->obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife();
        $this->obTransacao  = new Transacao ;
    }

    public function addLancamentoItem()
    {
       $this->arRAlmoxarifadoLancamentoItem[] = new RAlmoxarifadoLancamentoItem($this);
       $this->roUltimoLancamentoItem = &$this->arRAlmoxarifadoLancamentoItem[count($this->arRAlmoxarifadoLancamentoItem)-1];
    }

    public function listar($rsRecordSet, $stOrder = '', $boTransacao = '')
    {
        $stFiltro = "";
//        if ($this->getDescricao()) {
//           $stFiltro .= " WHERE descricao like '". $this->getDescricao() ."'";
//        }
        $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();
        $obErro = $obTAlmoxarifadoNaturezaLancamento->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function consultar($boTransacao = "")
    {
        $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();
        $obTAlmoxarifadoNaturezaLancamento->setDado( "cod_natureza" , $this->obRAlmoxarfadoNatureza->getCodigo() );
        $obTAlmoxarifadoNaturezaLancamento->setDado( "tipo_natureza", $this->obRAlmoxarifadoNatureza->getTipo());
        $obTAlmoxarifadoNaturezaLancamento->setDado( "exercicio_lancamento", $this->getExercicio());
        $obTAlmoxarifadoNaturezaLancamento->setDado( "num_lancamento", $this->getNumero() );
        $obErro = $obTAlmoxarifadoNaturezaLancamento->recuperaPorChave( $rsRecordSet, $boTransacao );
        if (!$obErro) {
           $this->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM( $rsRecordSet->getCampo("cgm_almoxarife") );
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
            $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();
            $obErro =  $obTAlmoxarifadoNaturezaLancamento->proximoCod( $inCodigo , $boTransacao );
            $this->setNumero($inCodigo);
            if ( !$obErro->ocorreu() ) {
                 $obTAlmoxarifadoNaturezaLancamento->setDado("num_lancamento"      , $this->getNumero() );
                 $obTAlmoxarifadoNaturezaLancamento->setDado("exercicio_lancamento", $this->getExercicio() );
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cod_natureza"         , $this->obRAlmoxarifadoNatureza->getCodigo());
                 $obTAlmoxarifadoNaturezaLancamento->setDado("tipo_natureza"    , $this->obRAlmoxarifadoNatureza->getTipo());
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cgm_almoxarife"     , $this->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM());
                 $obErro = $obTAlmoxarifadoNaturezaLancamento->inclusao( $boTransacao );
                 for ($i=0;$i<count($this->arRAlmoxarifadoLancamentoItem);$i++) {
                     $obRAlmoxarifadoLancamentoItem = $this->arRAlmoxarifadoLancamentoItem[$i];
                     $obErro = $obRAlmoxarifadoLancamentoItem->incluir($boTransacao);
                 }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoNaturezaLancamento);

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
            $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();
            if ( !$obErro->ocorreu() ) {
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cod_requisicao"      , $this->getCodigo() );
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoAlmoxarifado->getCodigo() );
                 $obTAlmoxarifadoNaturezaLancamento->setDado("exercicio"           , $this->getExercicio());
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cgm_requisitante"    , $this->obRCGMRequisitante->obRCGM->getNumCGM());
                 $obTAlmoxarifadoNaturezaLancamento->setDado("cgm_solicitante"     , $this->obRCGMSolicitante->getNumCGM());
                 $obTAlmoxarifadoNaturezaLancamento->setDado("observacao"          , $this->getObservacao());
                 $obErro = $obTAlmoxarifadoNaturezaLancamento->alteracao( $boTransacao );
                 $obRAlmoxarifadoLancamentoItem = new RAlmoxarifadoLancamentoItem($this);
                 $obRAlmoxarifadoLancamentoItem->excluir($boTransacao);
                 for ($i=0;$i<count($this->arRAlmoxarifadoLancamentoItem);$i++) {
                     $obRAlmoxarifadoLancamentoItem = $this->arRAlmoxarifadoLancamentoItem[$i];
                     $obRAlmoxarifadoLancamentoItem->incluir($boTransacao);
                 }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoNaturezaLancamento);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function excluir($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();

            for ($i=0;$i<count($this->arRAlmoxarifadoLancamentoItem);$i++) {
                $obRAlmoxarifadoLancamentoItem= $this->arRAlmoxarifadoLancamentoItem[$i];
                $obRAlmoxarifadoLancamentoItem->excluir($boTransacao);
            }

            $obTAlmoxarifadoNaturezaLancamento->setDado( "num_lancamento"         , $this->getNumero()           );
            $obTAlmoxarifadoNaturezaLancamento->setDado( "exercicio_lancamento"   , $this->getExercicio()        );
            $obTAlmoxarifadoNaturezaLancamento->setDado( "cod_natureza"           , $this->obRAlmoxarifadoNatureza->getCodigo());
            $obTAlmoxarifadoNaturezaLancamento->setDado( "tipo_natureza"       , $this->obRAlmoxarifadoNatureza->getTipo() );

            $obErro = $obTAlmoxarifadoNaturezaLancamento->exclusao( $boTransacao );
       }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoNaturezaLancamento);

        return $obErro;
    }

}
