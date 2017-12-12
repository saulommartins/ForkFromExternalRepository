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
    * Paginae Oculta para funcionalidade Manter Terminal
    * Data de Criação   : 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02

*/

/*
$Log$
Revision 1.8  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function montaLista($arRecordSet , $boExecuta = true)
{
        for ( $x = 0; $x < count( $arRecordSet ); $x++ ) {
            if ($arRecordSet[$x]['responsavel'] == 't') {
                $arRecordSet[$x]['responsavel_link'] = "<a href='javascript:mudaStatusResponsavel(\"".$arRecordSet[$x]['id_usuario']."\", \"f\")'>Sim</a>";
            } else {
                $arRecordSet[$x]['responsavel_link'] = "<a href='javascript:mudaStatusResponsavel(\"".$arRecordSet[$x]['id_usuario']."\", \"t\")'>Não</a>";
            }
        }

        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth( 60);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Responsável");
        $obLista->ultimoCabecalho->setWidth( 27 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_cgm" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "responsavel_link" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirUsuario();" );
        $obLista->ultimaAcao->addCampo("1","numcgm");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

      if ($boExecuta) {
            SistemaLegado::executaFrameOculto("parent.frames['telaPrincipal'].document.getElementById('spnLista').innerHTML = '".$stHTML."';");

      } else {
          return $stHTML;
      }
}

switch ($stCtrl) {
    case 'montaListaUsuario':
        $stHTML = montaLista( Sessao::read('arUsuario'), true );

    break;
    case 'mudaStatusResponsavel':
        $arUsuarioSessao = Sessao::read('arUsuario');
        if ($_GET['boNovoResponsavel']=='t') {
        $inCount = 0;
            foreach ($arUsuarioSessao as $arUsuario) {
                $arUsuario['responsavel']="f";
                $arUsuarioSessao[$inCount] = $arUsuario;
            $inCount++;
            }
        }
        $arUsuarioSessao[$_GET['inIdUsuario']]['responsavel'] = $_GET['boNovoResponsavel'];

        Sessao::write('arUsuario', $arUsuarioSessao);
        montaLista( $arUsuarioSessao );
    break;

    case 'incluirUsuario':
        $boErro = false;
        $arUsuario = Sessao::read('arUsuario');

        if ( count($arUsuario) > 0) {
            foreach ($arUsuario as $arTemp) {
                if ($_POST['inNumCgm'] == $arTemp['numcgm']) {
                    $boErro = true;
                }
            }
        }

        if (!$boErro) {
            $inCount = count(Sessao::read('arUsuario'));
            # Antigo teste onde permitia somente um responsável por terminal.
            #if ($_POST['boResponsavel']=="t") {
            #    $cont=0;
            #    while ($cont < $inCount) {
            #        $arUsuario[$cont]['responsavel'] = "f";
            #        $cont++;
            #    }
            #}
            $arUsuario[$inCount]['id_usuario']   = $inCount;
            $arUsuario[$inCount]['numcgm'  ]     = $_POST['inNumCgm'     ];
            $arUsuario[$inCount]['nom_cgm' ]     = $_POST['stNomCgm'     ];
            $arUsuario[$inCount]['responsavel']  = $_POST['boResponsavel'];
            Sessao::write('arUsuario', $arUsuario);

            $stHTML = montaLista( Sessao::read('arUsuario') );

        } else SistemaLegado::executaFrameOculto( "alertaAviso( 'O CGM escolhido já está na lista!()','form','erro','".Sessao::getId()."' );" );

    break;

    case 'excluirUsuario':
        $arArray = array();
        $inCount = 0;
        $arUsuario = Sessao::read('arUsuario');
        foreach ($arUsuario as $value) {
            if ( ($value['numcgm'] ) != $_GET['inNumCgm'] ) {
                $arArray[$inCount]['id_usuario']    = $inCount;
                $arArray[$inCount]['numcgm'       ] = $value['numcgm'         ];
                $arArray[$inCount]['nom_cgm'      ] = $value['nom_cgm'        ];
                $arArray[$inCount]['responsavel'  ] = $value['responsavel'    ];
                $inCount++;
            }
        }
        Sessao::write('arUsuario', $arArray);
        montaLista( Sessao::read('arUsuario') );
    break;

    case 'gerarCodigo':
        $stCodigo = sistemaLegado::gerarCodigoTerminal();
        $stJs = 'f.stCodVerificador.value = \''. $stCodigo .'\';';
        echo $stJs;
    break;
}
?>
