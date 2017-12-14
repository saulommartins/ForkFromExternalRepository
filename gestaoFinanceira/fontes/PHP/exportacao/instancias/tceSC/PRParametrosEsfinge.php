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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-01-18 14:07:50 -0200 (Qui, 18 Jan 2007) $

    * Casos de uso: uc-03.05.20
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ParametrosEsfinge";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

$jsOnLoad = "BloqueiaFrames(true,false);executaPaginaGeraArquivos();";

$sessao->filtro = array();
foreach ($_POST as $key=>$value) {
    $sessao->filtro[$key] = $value;
    if ( !is_array($value) ) {
        $pgAnterior .= "&$key=$value";
    }
}

//ATENÇÃO!!! Precisei fazer estes códigos porque se não o BloqueiFrames não funciona
// Ele dá erro no layer formulario e depois num esquema de botão
    $obLabel = new Label;
    $obLabel->setName("lblSituacao");
    $obLabel->setRotulo("Situação");
    $obLabel->setValue("Processando");

    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction      ( "xxx" );
    $obForm->setTarget      ( "telaPrincipal" ); //oculto - telaPrincipal

    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo    ( "Arquivos e-Sfinge" );
    $obFormulario->addComponente( $obLabel );

    $obBtn = new Button;
    $obBtn->setName( "Nada" );
    $obBtn->setValue( "Ok" );

    // $obFormulario->Ok();
    $obFormulario->defineBarra( array ( $obBtn ) );
    $obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
