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
/*
 * Titulo do arquivo : Oculto do Formulário de Suspensão de Edital
 * Data de Criação   : 05/12/2008

 * @author Analista      Gelson Wolowski Gonçalves
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterSuspensaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function editalInvalido()
{
    //Limpa o span relacionado a este campo
    $stJs.= "parent.frames['telaPrincipal'].document.getElementById('spnNumeroLicitacao').innerHTML = '';";
    $stJs.= "parent.frames['telaPrincipal'].document.getElementById('num_edital').value = '';";
    //Foco novamente no campo para ser digitado
    $stJs.= "parent.frames['telaPrincipal'].document.getElementById('num_edital').focus();";

    return $stJs;
}

switch ($stCtrl) {
    case 'exibeEdital':

        if (empty($_REQUEST['num_edital'])) {
            $stJs .= editalInvalido();
            break;
        }

        $arEdital = explode('/',$_REQUEST['num_edital']);
        if ($arEdital[1] == '') {
            $arEdital[1] = Sessao::getExercicio();
        }

        include_once(TLIC."TLicitacaoEdital.class.php");
        $obTLicitacaoEdital = new TLicitacaoEdital();
        $obTLicitacaoEdital->setDado( 'num_edital',$arEdital[0] );
        $obTLicitacaoEdital->setDado( 'exercicio' ,$arEdital[1] );

        $obTLicitacaoEdital->recuperaEditalSuspender($rsEdital);

        if ($rsEdital->eof()) {
            //Aviso de que o edital nao existe
            $stJs.= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." não encontrado!', 'form','erro','".Sessao::getId()."');";
            $stJs.= editalInvalido();
        } else {
            // Se o edital não está suspenso, pode ser feita a suspensão
            if ($rsEdital->getCampo('situacao') != 'Suspenso') {
                include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");
                $obSpnNumLicitacao = new Label();
                $obSpnNumLicitacao->setValue($rsEdital->getCampo('cod_licitacao').'/'.$rsEdital->getCampo('exercicio'));
                $obSpnNumLicitacao->setRotulo('Número da Licitação');

                $obLblEntidade = new Label();
                $obLblEntidade->setValue($rsEdital->getCampo('cod_entidade').' - '.$rsEdital->getCampo('nom_entidade'));
                $obLblEntidade->setRotulo('Entidade');

                $obLblModalidade = new Label();
                $obLblModalidade->setValue($rsEdital->getCampo('cod_modalidade').' - '.$rsEdital->getCampo('nom_modalidade'));
                $obLblModalidade->setRotulo('Modalidade');

                $obIlabelEditObjeto = new ILabelEditObjeto();
                $obIlabelEditObjeto->setCodObjeto($rsEdital->getCampo('cod_objeto'));
                $obIlabelEditObjeto->setRotulo('Objeto');

                $obTxtJustificativa = new TextArea;
                $obTxtJustificativa->setName  ("stJustificativa");
                $obTxtJustificativa->setId    ("stJustificativa");
                $obTxtJustificativa->setValue ('');
                $obTxtJustificativa->setRotulo("Justificativa" );
                $obTxtJustificativa->setTitle ("Informe a Justificativa.");
                $obTxtJustificativa->setNull  (false);

                $obFormulario = new Formulario();
                $obFormulario->addComponente($obLblEntidade);
                $obFormulario->addComponente($obSpnNumLicitacao);
                $obFormulario->addComponente($obIlabelEditObjeto);
                $obFormulario->addComponente($obLblModalidade);
                $obFormulario->addComponente($obTxtJustificativa);
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs.="d.getElementById('spnNumeroLicitacao').innerHTML = '".$stHTML."';           \n";

                $stJs.="f.cod_licitacao.value  = '".$rsEdital->getCampo('cod_licitacao')."';  \n";
                $stJs.="f.num_edital.value = '" . $arEdital[0] .'/' . $arEdital[1]  . "';           \n";
                $stJs.="f.cod_modalidade.value = '".$rsEdital->getCampo('cod_modalidade')."'; \n";
                $stJs.="f.cod_entidade.value   = '".$rsEdital->getCampo('cod_entidade')."';   \n";
                $stJs.="f.exercicio.value      = '".$rsEdital->getCampo('exercicio')."';      \n";

                $inCount = 0;
            } else {
                $stJs.= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." já está suspenso!', 'form','erro','".Sessao::getId()."');";
                $stJs.= editalInvalido();
            }

        }//fim do else

    break;

    case "exibeEditalSuspenso":
        if (empty($_REQUEST['num_edital'])) {
            $stJs .= editalInvalido();
            break;
        }

        $arEdital = explode('/',$_REQUEST['num_edital']);
        if ($arEdital[1] == '') {
            $arEdital[1] = Sessao::getExercicio();
        }

        include_once(TLIC."TLicitacaoEdital.class.php");
        $obTLicitacaoEdital = new TLicitacaoEdital();
        $obTLicitacaoEdital->setDado( 'num_edital',$arEdital[0] );
        $obTLicitacaoEdital->setDado( 'exercicio' ,$arEdital[1] );

        $obTLicitacaoEdital->recuperaEditalSuspender($rsEdital);

        if ($rsEdital->eof()) {
            //Aviso de que o edital nao existe
            $stJs.= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." não encontrado!', 'form','erro','".Sessao::getId()."');";
            $stJs.= editalInvalido();
        } else {
            // Somente se o edital estiver suspenso pode ser feita anulação de suspensão
            if ($rsEdital->getCampo('situacao') == 'Suspenso') {
                include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");
                $obSpnNumLicitacao = new Label();
                $obSpnNumLicitacao->setValue($rsEdital->getCampo('cod_licitacao').'/'.$rsEdital->getCampo('exercicio'));
                $obSpnNumLicitacao->setRotulo('Número da Licitação');

                $obLblEntidade = new Label();
                $obLblEntidade->setValue($rsEdital->getCampo('cod_entidade').' - '.$rsEdital->getCampo('nom_entidade'));
                $obLblEntidade->setRotulo('Entidade');

                $obLblModalidade = new Label();
                $obLblModalidade->setValue($rsEdital->getCampo('cod_modalidade').' - '.$rsEdital->getCampo('nom_modalidade'));
                $obLblModalidade->setRotulo('Modalidade');

                $obIlabelEditObjeto = new ILabelEditObjeto();
                $obIlabelEditObjeto->setCodObjeto($rsEdital->getCampo('cod_objeto'));
                $obIlabelEditObjeto->setRotulo('Objeto');

                $obLblJustificativa = new Label();
                $obLblJustificativa->setValue($rsEdital->getCampo('justificativa'));
                $obLblJustificativa->setRotulo('Justificativa');

                $obFormulario = new Formulario();
                $obFormulario->addComponente($obLblEntidade);
                $obFormulario->addComponente($obSpnNumLicitacao);
                $obFormulario->addComponente($obIlabelEditObjeto);
                $obFormulario->addComponente($obLblModalidade);
                $obFormulario->addComponente($obLblJustificativa);
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs.="d.getElementById('spnNumeroLicitacao').innerHTML = '".$stHTML."';           \n";

                $stJs.="f.cod_licitacao.value  = '".$rsEdital->getCampo('cod_licitacao')."';  \n";
                $stJs.="f.num_edital.value = '" . $arEdital[0] .'/' . $arEdital[1]  . "';           \n";
                $stJs.="f.cod_modalidade.value = '".$rsEdital->getCampo('cod_modalidade')."'; \n";
                $stJs.="f.cod_entidade.value   = '".$rsEdital->getCampo('cod_entidade')."';   \n";
                $stJs.="f.exercicio.value      = '".$rsEdital->getCampo('exercicio')."';      \n";

                $inCount = 0;
            } else {
                $stJs.= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." não está suspenso!', 'form','erro','".Sessao::getId()."');";
                $stJs.= editalInvalido();
            }
        }//fim do else

    break;

}// fim do SWITCH
echo $stJs;
?>
