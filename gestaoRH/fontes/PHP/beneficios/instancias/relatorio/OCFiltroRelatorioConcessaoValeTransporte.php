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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.06.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioGrupoConcessao.class.php" );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php" );

function geraSpan()
{
    $obFormulario = new Formulario;
    switch ($_POST['stRdoOpcoes']) {

        case 'contrato':
            $obFiltroContrato = new IFiltroContrato();
            $obFiltroContrato->geraFormulario( $obFormulario );
        break;

        case 'cgm':
            $obFiltroCGMContrato = new IFiltroCGMContrato();
            $obFiltroCGMContrato->setTipoContrato("vigente");
            $obFiltroCGMContrato->geraFormulario( $obFormulario );

        break;

        case 'grupo':
            $obTxtCodGrupo = new TextBox;
            $obTxtCodGrupo->setRotulo            ( "Grupo"      );
            $obTxtCodGrupo->setName              ( "inCodGrupo" );
            $obTxtCodGrupo->setId                ( "inCodGrupo" );
            $obTxtCodGrupo->setValue             ( ""           );
            $obTxtCodGrupo->setMaxLength         ( 10           );
            $obTxtCodGrupo->setSize              ( 10           );
            $obTxtCodGrupo->setInteiro           ( true         );
            $obTxtCodGrupo->setNull              ( true         );

            $obRBeneficioGrupoConcessao = new RBeneficioGrupoConcessao;
            $obRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo);

            $obCmbGrupo = new Select;
            $obCmbGrupo->setName                 ( "stGrupo"        );
            $obCmbGrupo->setId                   ( "stGrupo"        );
            $obCmbGrupo->setStyle                ( "width: 250px"   );
            $obCmbGrupo->setRotulo               ( "Grupos"         );
            $obCmbGrupo->setValue                ( ""               );
            $obCmbGrupo->setNull                 ( true             );
            $obCmbGrupo->addOption               ( "", "Selecione"  );
            $obCmbGrupo->setCampoID              ( "[cod_grupo]"    );
            $obCmbGrupo->setCampoDesc            ( "[descricao]"    );
            $obCmbGrupo->preencheCombo           ( $rsGrupo         );

            $obFormulario->addTitulo             ( "Filtro por Grupo"                                 );
            $obFormulario->addComponenteComposto ( $obTxtCodGrupo , $obCmbGrupo                              );
        break;
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML ='".$obFormulario->getHTML()."';\n" ;
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';\n";

    return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "geraSpan":
        $stJs .= geraSpan();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
