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
 * Componente que calcula o saldo da entidade
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 * $Id: $
 *
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_TES_NEGOCIO . 'RTesourariaSaldoTesouraria.class.php';
include_once CAM_GF_TES_MAPEAMENTO . 'FTesourariaExtratoBancario.class.php';
include_once CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoConfiguracaoEntidade.class.php';

class ISaldoCaixa
{
    public $obFTesourariaExtratoBancario;
    public $obTAdministracaoConfiguracaoEntidade;
    public $inCodEntidade;
    public $stJavascript;

    public function __construct()
    {
        $this->obFTesourariaExtratoBancario       = new FTesourariaExtratoBancario();
        $this->obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
    }

    public function montaSaldo()
    {
        $this->getSaldoCaixa($flSaldoCaixa);
        $stHTMLAux =  ' - Saldo da Conta Caixa: R$ ';
        if ($flSaldoCaixa < 0) {
            $stHTMLAux .= "<span style=\"color:red;\">-".number_format(abs($flSaldoCaixa), 2, ',', '.').'</span>';
        } else {
            $stHTMLAux .= number_format($flSaldoCaixa, 2, ',', '.');
        }
        //$this->stJavascript = "jq('#stTerminalLogado').html(jq('#stTerminalLogado').html()+'" . $stHTMLAux . "');";
        $this->stJavascript = "jq('#stSaldoCaixa',top.telaStatus.document).html('" . $stHTMLAux . "');";

        return $this->stJavascript;
    }

    public function getSaldoCaixa(&$flSaldoCaixa)
    {
        $stFiltro = "";
        $stOrder  = "";
        $obErro   = new Erro;

        $this->getCodPlanoEntidade($inCodPlano);
        if ($inCodPlano != '') {
            $this->obFTesourariaExtratoBancario = new FTesourariaExtratoBancario;
            $this->obFTesourariaExtratoBancario->setDado("inCodPlano"    , $inCodPlano );
            $this->obFTesourariaExtratoBancario->setDado("stExercicio"   , Sessao::getExercicio());
            $this->obFTesourariaExtratoBancario->setDado("stDtInicial" , '01/01/'.Sessao::getExercicio());
            $this->obFTesourariaExtratoBancario->setDado("stDtFinal"   , '31/12/'.Sessao::getExercicio());
            $this->obFTesourariaExtratoBancario->setDado("boMovimentacao", "true" );
            $obErro = $this->obFTesourariaExtratoBancario->recuperaSaldoAnteriorAtual( $rsSaldo, $stFiltro, $stOrder );
            $flSaldoCaixa = $rsSaldo->getCampo("fn_saldo_conta_tesouraria");
        }

        return $obErro;
    }

    public function getCodPlanoEntidade(&$inCodPlano)
    {
        $stFiltro = " WHERE parametro = 'conta_caixa'
                        AND cod_entidade = " . $this->inCodEntidade . "
                        AND exercicio = '" . Sessao::getExercicio() . "'
        ";
        $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsCodPlano, $stFiltro);
        $inCodPlano = $rsCodPlano->getCampo('valor');

        return $obErro;
    }

}
?>
