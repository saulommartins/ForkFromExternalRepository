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
 * Página do Oculto de Retenções de Ordens de Pagamentos
 *
 * @category   Urbem
 * @package    Empenho
 * @ignore     Relatorio
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 * Casos de uso: uc-02.03.40
 */

/* includes do sistema */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/* includes dos componentes */
include CAM_GF_TES_NEGOCIO."RTesourariaRelatorioResumoReceita.class.php";

switch ($_REQUEST['stCtrl']) {
case 'mostraSpanReceita':

    switch ($_REQUEST['stTipoReceita']) {
    case 'orcamentaria':
        include CAM_GF_ORC_COMPONENTES."IIntervaloPopUpReceita.class.php";
        $obIIntervaloPopUpReceita = new IIntervaloPopUpReceita();
        $obIIntervaloPopUpReceita->obIPopUpReceitaInicial->setUsaFiltro(true);
        $obIIntervaloPopUpReceita->obIPopUpReceitaInicial->obCampoCod->setName('inReceitaInicial');

        $obIIntervaloPopUpReceita->obIPopUpReceitaFinal->setUsaFiltro(true);
        $obIIntervaloPopUpReceita->obIPopUpReceitaFinal->obCampoCod->setName('inReceitaFinal');

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obIIntervaloPopUpReceita);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        echo "jq('#spnReceitas').html('".$stHTML."');";
        break;

    case 'extra':
        include CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php";
        $obIPopUpContaAnalitica = new IIntervaloPopUpContaAnalitica;
        $obIPopUpContaAnalitica->setRotulo('Conta de Receita');
        $obIPopUpContaAnalitica->setTitle('Informe o código da conta de receita.');
        $obIPopUpContaAnalitica->obIPopUpContaAnaliticaInicial->setTipoBusca('tes_arrecadacao_extra_receita');
        $obIPopUpContaAnalitica->obIPopUpContaAnaliticaInicial->obCampoCod->setName('inReceitaInicial');

        $obIPopUpContaAnalitica->obIPopUpContaAnaliticaFinal->setTipoBusca('tes_arrecadacao_extra_receita');
        $obIPopUpContaAnalitica->obIPopUpContaAnaliticaFinal->obCampoCod->setName('inReceitaFinal');

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obIPopUpContaAnalitica);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        echo "jq('#spnReceitas').html('".$stHTML."');";
        break;

        default:
            echo "jq('#spnReceitas').html('');";
    }
    break;

case 'validaSituacao':
    
    if ($_REQUEST['inSituacao'] == 2) {
        $stJs  = " jq('input:radio[name=boDataPagamento][value=S]').attr('checked','false'); ";    
        $stJs .= " jq('input:radio[name=boDataPagamento][value=N]').attr('checked','true'); ";            
        $stJs .= " jq('#boDataPagamento').attr('disabled','true'); ";    
    }else{
        $stJs = " jq('#boDataPagamento').removeAttr('disabled'); ";    
    }

    echo $stJs;
    break;  


}

?>
