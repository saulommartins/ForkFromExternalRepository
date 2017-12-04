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

Revision 1.4  2006/07/06 12:09:31  diego

*/

//include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoRequisicao.class.php"                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoNaturezaLancamento.class.php"                  );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoLancamentoRequisicaoItem.class.php"               );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php"               );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoLancamentoRequisicao extends RAlmoxarifadoNaturezaLancamento
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
    public $obRAlmoxarifadoRequisicao;

    /**
        * @access Private
        * @var Integer
    */

    public $roUltimoLancamentoRequisicaoItem;

    /**
        * @access Private
        * @var Array
    */

    public $arRAlmoxarifadoLancamentoRequisicaoItem;

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoLancamentoRequisicao()
    {
        $this->obRAlmoxarifadoNatureza = new RAlmoxarifadoNatureza();
        $this->obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife();
        $this->obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao();
        $this->obTransacao  = new Transacao ;
    }

    public function addLancamentoRequisicaoItem()
    {
       $this->arRAlmoxarifadoLancamentoRequisicaoItem[] = new RAlmoxarifadoLancamentoRequisicaoItem($this);
       $this->roUltimoLancamentoRequisicaoItem = &$this->arRAlmoxarifadoLancamentoRequisicaoItem[count($this->arRAlmoxarifadoLancamentoRequisicaoItem)-1];
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
           $this->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM( $rsRecordSet->getCampo("cgm_almoxarife") );        }

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
            $obErro = parent::incluir($boTransacao);
            if ( !$obErro->ocorreu() ) {
                 for ($i=0;$i<count($this->arRAlmoxarifadoLancamentoRequisicaoItem);$i++) {
                     $obRAlmoxarifadoLancamentoRequisicaoItem = $this->arRAlmoxarifadoLancamentoRequisicaoItem[$i];
                     $obErro = $obRAlmoxarifadoLancamentoRequisicaoItem->incluir($boTransacao);
                 }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoLancamentoItem);

        return $obErro;
    }

}
