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
* Arquivo instância para popup de Objeto
* Data de Criação: 07/03/2006

* @author Desenvolvedor: Leandro André Zis

* Casos de uso :uc-03.05.22
*/

/*
$Log$
Revision 1.2  2006/11/28 10:25:37  leandro.zis
corrigido caso de uso

Revision 1.1  2006/11/01 19:58:21  leandro.zis
atualizado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TLicitacaoLicitacao.class.php");

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ $stCampoCod ];

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        if ($inCodigo != "") {
            $rsLicitacao = new RecordSet;
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado('cod_licitacao', $inCodigo );
            $obTLicitacaoLicitacao->setDado('exercicio', $stExercicio);
            $obTLicitacaoLicitacao->setDado('cod_entidade', $inCodEntidade);
            $obTLicitacaoLicitacao->setDado('cod_modalidade', $inCodModalidade);
            $obTLicitacaoLicitacao->recuperaRelacionamento($rsLicitacao);
            $stObjeto = $rsLicitacao->getCampo('descricao');
            $stDescEntidade = $rsLicitacao->getCampo('desc_entidade');
            $stDescModalidade = $rsLicitacao->getCampo('desc_modalidade');

            $obFormulario = new Formulario;
            $obLblExercicio = new Label;
            $obLblExercicio->setValue($stExercicio);
            $obLblEntidade = new Label;
            $obLblEntidade->setValue($stDescEntidade);
            $obLblModalidade = new Label;
            $obLblModalidade->setValue($stDescModalidade);
            $obFormulario->addComponente($obLblExercicio);
            $obFormulario->addComponente($obLblEntidade);
            $obFormulario->addComponente($obLblModalidade);
            $obFormulario->geraHTML();

            $stJs .= "d.getElementById('".$stCampoDesc."').value = '".$stLicitacao."';";
            $stJs .= "d.getElementById('spnInfoAdcional').value = '.".$obFormulario->getHTML()."';";
            $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stLicitacao."');";
            if (!$stLicitacao) {
                $stJs .= "alertaAviso('@Código da Licitação(". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }
        sistemaLegado::executaFrameOculto( $stJs );
    break;

}

?>
