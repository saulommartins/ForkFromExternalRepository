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
    * Pagina Oculta para Formulário de Manter Fornecedor
    * Data de Criação   : 01/09/2005

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.04.03

    $Id: OCManterFornecedor.php 63833 2015-10-22 13:05:17Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );
include_once( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
include_once(CAM_GA_CGM_COMPONENTES."ITextBoxPisPasep.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$inNumCgm = ($_REQUEST['inCGM'] ? $_REQUEST['inCGM'] : Sessao::read('inCGM') );
$obTCGM = new TCGMPessoaFisica();
$obTCGM->setDado('numcgm',$inNumCgm);

function montaListaContaBancaria($arRecordSet , $boExecuta = true)
{
        global $pgOcul;
        $rsListaContaBancaria = new RecordSet;
        $rsListaContaBancaria->preenche( $arRecordSet );

        $obListaContaBancaria = new Lista;
        $obListaContaBancaria->setTitulo('Contas Bancárias');
        $obListaContaBancaria->setMostraPaginacao( false );
        $obListaContaBancaria->setRecordSet( $rsListaContaBancaria );

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 5 );
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("Banco");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 23);
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("Agência");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 23 );
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("Conta");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 10);
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("Padrão");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 15 );
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addCabecalho();
        $obListaContaBancaria->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaContaBancaria->ultimoCabecalho->setWidth( 5 );
        $obListaContaBancaria->commitCabecalho();

        $obListaContaBancaria->addDado();
        $obListaContaBancaria->ultimoDado->setCampo( "[banco] - [nom_banco]" );
        $obListaContaBancaria->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaContaBancaria->commitDado();

        $obListaContaBancaria->addDado();
        $obListaContaBancaria->ultimoDado->setCampo( "[agencia] - [nom_agencia]" );
        $obListaContaBancaria->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaContaBancaria->commitDado();

        $obListaContaBancaria->addDado();
        $obListaContaBancaria->ultimoDado->setCampo( "conta" );
        $obListaContaBancaria->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaContaBancaria->commitDado();

        $obChkPrevidencia = new CheckBox;
        $obChkPrevidencia->setName           ( 'chkContaBanco_'  );
        $obChkPrevidencia->setValue          ( 'padrao' );
        $obChkPrevidencia->obEvento->setOnClick(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&'+this.name+'='+this.value,'validaPadrao');");
        $obListaContaBancaria->addDadoComponente( $obChkPrevidencia );
        $obListaContaBancaria->ultimoDado->setCampo( "padrao" );
        $obListaContaBancaria->ultimoDado->setAlinhamento('CENTRO');
        $obListaContaBancaria->commitDadoComponente();

        $obListaContaBancaria->addAcao();
        $obListaContaBancaria->ultimaAcao->setAcao( "EXCLUIR" );
        $obListaContaBancaria->ultimaAcao->setFuncaoAjax( true );
        $obListaContaBancaria->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirContaBancaria');" );
        $obListaContaBancaria->ultimaAcao->addCampo("1","banco");
        $obListaContaBancaria->ultimaAcao->addCampo("2","agencia");
        $obListaContaBancaria->ultimaAcao->addCampo("3","conta");
        $obListaContaBancaria->commitAcao();

        $obListaContaBancaria->addAcao();
        $obListaContaBancaria->ultimaAcao->setAcao( "ALTERAR" );
        $obListaContaBancaria->ultimaAcao->setFuncaoAjax( true );
        $obListaContaBancaria->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alteraContaBancaria');" );
        $obListaContaBancaria->ultimaAcao->addCampo("1","banco");
        $obListaContaBancaria->ultimaAcao->addCampo("2","agencia");
        $obListaContaBancaria->ultimaAcao->addCampo("3","conta");
        $obListaContaBancaria->commitAcao();

        $obListaContaBancaria->montaHTML();
        $stHTML = $obListaContaBancaria->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

      if ($boExecuta) {
             $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaContaBancaria').innerHTML = '".$stHTML."';";

          return $stJs;
      } else {
          return $stHTML;
      }
}

function montaListaSocio($arRecordSet , $boExecuta = true)
{
        global $pgOcul;
        $rsListaSocio = new RecordSet;
        $rsListaSocio->preenche( $arRecordSet );

        $obListaSocio = new Lista;
        $obListaSocio->setTitulo('Lista de Sócios');
        $obListaSocio->setMostraPaginacao( false );
        $obListaSocio->setRecordSet( $rsListaSocio );

        $obListaSocio->addCabecalho();
        $obListaSocio->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaSocio->ultimoCabecalho->setWidth( 5 );
        $obListaSocio->commitCabecalho();

        $obListaSocio->addCabecalho();
        $obListaSocio->ultimoCabecalho->addConteudo("Sócio");
        $obListaSocio->ultimoCabecalho->setWidth( 23);
        $obListaSocio->commitCabecalho();

        $obListaSocio->addCabecalho();
        $obListaSocio->ultimoCabecalho->addConteudo("Tipo");
        $obListaSocio->ultimoCabecalho->setWidth( 23 );
        $obListaSocio->commitCabecalho();

        $obListaSocio->addCabecalho();
        $obListaSocio->ultimoCabecalho->addConteudo("Ativo");
        $obListaSocio->ultimoCabecalho->setWidth( 10);
        $obListaSocio->commitCabecalho();

        $obListaSocio->addCabecalho();
        $obListaSocio->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaSocio->ultimoCabecalho->setWidth( 5 );
        $obListaSocio->commitCabecalho();

        $obListaSocio->addDado();
        $obListaSocio->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
        $obListaSocio->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaSocio->commitDado();

        $obListaSocio->addDado();
        $obListaSocio->ultimoDado->setCampo( "[cod_tipo] - [descricao]" );
        $obListaSocio->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaSocio->commitDado();

        $obListaSocio->addDado();
        $obListaSocio->ultimoDado->setCampo( "ativo_descricao" );
        $obListaSocio->ultimoDado->setAlinhamento( 'CENTRO' );
        $obListaSocio->commitDado();

        $obListaSocio->addAcao();
        $obListaSocio->ultimaAcao->setAcao( "EXCLUIR" );
        $obListaSocio->ultimaAcao->setFuncaoAjax( true );
        $obListaSocio->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirSocio');" );
        $obListaSocio->ultimaAcao->addCampo("1", "id");
        $obListaSocio->commitAcao();

        $obListaSocio->montaHTML();
        $stHTML = $obListaSocio->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

      if ($boExecuta) {
             $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaSocio').innerHTML = '".$stHTML."';";

          return $stJs;
      } else {
          return $stHTML;
      }
}

function montaListaFornecedor($arRecordSet , $boExecuta = true)
{
        global $pgOcul;
        $rsListaFornecedor = new RecordSet;
        $rsListaFornecedor->preenche( $arRecordSet );

        $obListaFornecedor = new Lista;
        $obListaFornecedor->setTitulo('Classificação do Fornecedor');
        $obListaFornecedor->setMostraPaginacao( false );
        $obListaFornecedor->setRecordSet( $rsListaFornecedor );

        $obListaFornecedor->addCabecalho();
        $obListaFornecedor->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaFornecedor->ultimoCabecalho->setWidth( 5 );
        $obListaFornecedor->commitCabecalho();

        $obListaFornecedor->addCabecalho();
        $obListaFornecedor->ultimoCabecalho->addConteudo("Catálogo");
        $obListaFornecedor->ultimoCabecalho->setWidth( 15);
        $obListaFornecedor->commitCabecalho();

        $obListaFornecedor->addCabecalho();
        $obListaFornecedor->ultimoCabecalho->addConteudo("Classificação");
        $obListaFornecedor->ultimoCabecalho->setWidth( 20 );
        $obListaFornecedor->commitCabecalho();

        $obListaFornecedor->addCabecalho();
        $obListaFornecedor->ultimoCabecalho->addConteudo("Descrição");
        $obListaFornecedor->ultimoCabecalho->setWidth( 20);
        $obListaFornecedor->commitCabecalho();

        $obListaFornecedor->addCabecalho();
        $obListaFornecedor->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaFornecedor->ultimoCabecalho->setWidth( 5 );
        $obListaFornecedor->commitCabecalho();

        $obListaFornecedor->addDado();
        $obListaFornecedor->ultimoDado->setCampo( "catalogo" );
        $obListaFornecedor->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaFornecedor->commitDado();

        $obListaFornecedor->addDado();
        $obListaFornecedor->ultimoDado->setCampo( 'classificacao');
        $obListaFornecedor->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaFornecedor->commitDado();

        $obListaFornecedor->addDado();
        $obListaFornecedor->ultimoDado->setCampo( "descricao" );
        $obListaFornecedor->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaFornecedor->commitDado();

        $obListaFornecedor->addAcao();
        $obListaFornecedor->ultimaAcao->setAcao( "EXCLUIR" );
        $obListaFornecedor->ultimaAcao->setFuncaoAjax( true );
        $obListaFornecedor->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirFornecedor');" );
        $obListaFornecedor->ultimaAcao->addCampo("1","cod_catalogo");
        $obListaFornecedor->ultimaAcao->addCampo("2","classificacao");
        $obListaFornecedor->commitAcao();

        $obListaFornecedor->montaHTML();
        $stHTML = $obListaFornecedor->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

      if ($boExecuta) {
        $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaFornecedor').innerHTML = '".$stHTML."';";

        return $stJs;
      } else {
          return $stHTML;
      }
}

function montaListaAtividade($arRecordSet , $boExecuta = true)
{
        $rsListaAtividade = new RecordSet;
        $rsListaAtividade->preenche( $arRecordSet );
        $obListaAtividade = new Lista;
        $obListaAtividade->setTitulo('Ramos de Atividade');
        $obListaAtividade->setMostraPaginacao( false );
        $obListaAtividade->setRecordSet( $rsListaAtividade );
        $obListaAtividade->addCabecalho();
        $obListaAtividade->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaAtividade->ultimoCabecalho->setWidth( 5 );
        $obListaAtividade->commitCabecalho();
        $obListaAtividade->addCabecalho();
        $obListaAtividade->ultimoCabecalho->addConteudo("Código");
        $obListaAtividade->ultimoCabecalho->setWidth( 15);
        $obListaAtividade->commitCabecalho();
        $obListaAtividade->addCabecalho();
        $obListaAtividade->ultimoCabecalho->addConteudo("Atividade");
        $obListaAtividade->ultimoCabecalho->setWidth( 20 );
        $obListaAtividade->commitCabecalho();
        $obListaAtividade->addCabecalho();
        $obListaAtividade->ultimoCabecalho->addConteudo("&nbsp;");
        $obListaAtividade->ultimoCabecalho->setWidth( 5 );
        $obListaAtividade->commitCabecalho();

        $obListaAtividade->addDado();
        $obListaAtividade->ultimoDado->setCampo( "codigo" );
        $obListaAtividade->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaAtividade->commitDado();

        $obListaAtividade->addDado();
        $obListaAtividade->ultimoDado->setCampo( 'atividade');
        $obListaAtividade->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obListaAtividade->commitDado();

        $obListaAtividade->addAcao();
        $obListaAtividade->ultimaAcao->setAcao( "EXCLUIR" );
        $obListaAtividade->ultimaAcao->setFuncao( true );
        $obListaAtividade->ultimaAcao->setLink( "JavaScript:excluirAtividade();" );
        $obListaAtividade->ultimaAcao->addCampo("1","codigo");
        $obListaAtividade->commitAcao();

        $obListaAtividade->montaHTML();
        $stHTML = $obListaAtividade->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

      if ($boExecuta) {
         SistemaLegado::executaFrameOculto("parent.frames['telaPrincipal'].document.getElementById('spnListaAtividade').innerHTML = '".$stHTML."';");

      } else {
          $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaAtividade').innerHTML = '".$stHTML."';";

        return $stJs;
      }
}

function alterarContaBancaria($CodBanco , $codAgencia , $NumConta)
{
    include_once CAM_GT_MON_INSTANCIAS."agenciaBancaria/OCMontaAgencia.php";

    $stJs = "";
    $stJs .= "jq('#inCodBancoTxt').val('".$CodBanco."'); \n";
    $stJs .= "jq('#inCodBanco').val('".$CodBanco."'); \n";

    include_once CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php";
    $obTMONBanco = new TMONBanco;
    $stFiltro = " where num_banco = '".$CodBanco."'" ;
    $obTMONBanco->recuperaTodos($rsBanco, $stFiltro);

    include_once CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php";
    $obTMONAgencia = new TMONAgencia;
    $stFiltro = ' where cod_banco = '.$rsBanco->getCampo('cod_banco');
    $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);
    $inCount = 1;
    while (!$rsAgencia->eof()) {
       $inId   = $rsAgencia->getCampo("num_agencia");
       $stDesc = $rsAgencia->getCampo("nom_agencia");

       $stJs .= "f.stNumAgencia.options[".$inCount."] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
       $rsAgencia->proximo();
       $inCount++;
    }

    $stJs.= "f.stContaCorrente.value = '$NumConta';\n";
    $stJs.= "document.getElementById('stNumAgenciaTxt').value = '$codAgencia';\n";
    $stJs.= "f.stNumAgencia.value = '$codAgencia';\n";

    $stJs.="document.frm.btIncluirContaBancaria.disabled          = true ;\n";
    $stJs.="document.frm.btAlterarContaBancaria.disabled          = false;\n";
    $stJs.="document.frm.stChaveConta.value    = '$CodBanco$codAgencia$NumConta';\n";

    return $stJs ;
}

function retornaPisPasep()
{
    global $obTCGM;
    $obTCGM->recuperaPorChave($rsCGM);
    if ( !$rsCGM->eof() ) {
        $obTTextBoxPisPasep = new ITextBoxPisPasep();
        $obTTextBoxPisPasep->setValue($rsCGM->getCampo('servidor_pis_pasep'));

        $obFormulario = new Formulario();
        $obFormulario->addComponente( $obTTextBoxPisPasep );

        if ($rsCGM->getCampo('servidor_pis_pasep'))
            $obTTextBoxPisPasep->setReadOnly( true );

        $obFormulario->montaInnerHTML();
        $stEval  = str_replace("\n","",$obTTextBoxPisPasep->getHTML() );
        $stJs = "document.getElementById('spnPisPasep').innerHTML = '".$obFormulario->getHTML()."';\n";
    } else {
        $stJs = "document.getElementById('spnPisPasep').innerHTML = '';\n";
    }

    return $stJs;
}

switch ($stCtrl) {

    case 'incluirContaBancaria':
        $boErro = false;
        $arContaBancariaSessao = Sessao::read('arContaBancaria');
        if ($arContaBancariaSessao) {
            foreach ($arContaBancariaSessao as $arContaBancaria) {
                if( $_REQUEST['inCodBancoTxt'] == $arContaBancaria['banco'] && $_REQUEST['stNumAgencia'] == $arContaBancaria['agencia'] && $_REQUEST['stContaCorrente'] == $arContaBancaria['conta'])
                    $boErro = true;
            }
        }
        if (!$boErro) {
           $inCount = count($arContaBancariaSessao);
           $cont=0;
           $boPadrao = true;
           $arContaBancaria = $arContaBancariaSessao;
           while ($cont < $inCount) {
               if ($arContaBancaria[$cont]['padrao'] == true)
                   $boPadrao = false;
               $cont++;
           }
           include_once(TMON."TMONBanco.class.php");
           $obTMONBanco = new TMONBanco() ;
           $stFiltro = " WHERE  num_banco = '".$_REQUEST['inCodBancoTxt']."' ";
           $obTMONBanco->recuperaTodos( $rsBanco, $stFiltro );

           include_once(TMON."TMONAgencia.class.php");
           $obTMONAgencia = new TMONAgencia() ;
           $stFiltro = " WHERE cod_banco = '".$rsBanco->getCampo('cod_banco')."'
                              AND num_agencia = '".$_REQUEST['stNumAgencia']."' " ;
           $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);

           $inCount = sizeof($arContaBancariaSessao);
           $arContaBancaria[$inCount]['id_usuario'] = $inCount;
           $arContaBancaria[$inCount]['banco'    ]  = $_REQUEST['inCodBancoTxt' ];
           $arContaBancaria[$inCount]['nom_banco']  = $rsBanco->getCampo('nom_banco');
           $arContaBancaria[$inCount]['agencia'  ]  = $_REQUEST['stNumAgencia'];
           $arContaBancaria[$inCount]['nom_agencia']= $rsAgencia->getCampo('nom_agencia');
           $arContaBancaria[$inCount]['conta'    ]  = $_REQUEST['stContaCorrente'];
           $arContaBancaria[$inCount]['padrao'   ]  = $boPadrao;
           $arContaBancaria[$inCount]['cod_banco'   ]  = $rsBanco->getCampo('cod_banco');
           $arContaBancaria[$inCount]['cod_agencia' ]  = $rsAgencia->getCampo('cod_agencia');
           Sessao::write('arContaBancaria' , $arContaBancaria);
           $stJs .= montaListaContaBancaria( $arContaBancaria );

        } else {
            $stJs .= "alertaAviso( 'A conta escolhida já está na lista!','form','erro','".Sessao::getId()."' );";
        }

        $stJs .= "jq('#stContaCorrente').val('');";
        echo $stJs;

    break;
    case 'excluirContaBancaria':
        $arContaBancaria = array();
        $inCount = 0;
        $key = trim($_REQUEST['banco']."@%@".$_REQUEST['agencia']."@%@".$_REQUEST['conta']);
        foreach ( Sessao::read('arContaBancaria') as $value ) {
            $keyValue = trim($value['banco']."@%@".$value['agencia']."@%@".$value['conta']);
            if ($key != $keyValue) {
                $arContaBancaria[$inCount]['id_usuario']  = $inCount;
                $arContaBancaria[$inCount]['banco'  ]     = $value['banco'  ];
                $arContaBancaria[$inCount]['agencia']     = $value['agencia'];
                $arContaBancaria[$inCount]['conta'  ]     = $value['conta'  ];
                $arContaBancaria[$inCount]['padrao' ]     = $value['padrao' ];
                $arContaBancaria[$inCount]['cod_banco']   = $value['cod_banco'];
                $arContaBancaria[$inCount]['cod_agencia'] = $value['cod_agencia'];
                $arContaBancaria[$inCount]['nom_agencia'] = $value['nom_agencia'];
                $arContaBancaria[$inCount]['nom_banco']   = $value['nom_banco'];
                $inCount++;
            }
        }
        Sessao::write('arContaBancaria' , array());
        Sessao::write('arContaBancaria' , $arContaBancaria);
        $stJs .= montaListaContaBancaria( $arContaBancaria );
        echo $stJs;
    break;

    case 'incluirSocio':
        include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasTipoSocio.class.php' );
        
        $arSocioSessao = Sessao::read('arSocio');
        $inCount = sizeof($arSocioSessao);
        
        if($inCount > 0) {
            foreach ($arSocioSessao as $arSocio) {
                if($_REQUEST['cgmSocio'] == $arSocio['numcgm'] && (($_REQUEST['inCodTipo'] == $arSocio['cod_tipo']) || $arSocio['cod_tipo'] == 3 || $_REQUEST['inCodTipo'] == 3)) {
                        echo "alertaAviso( 'O sócio escolhido já está na lista!','form','erro','".Sessao::getId()."' );";
                        return false;
                }
            }
        }
        
        $rsCGM  = new Recordset;
        $obTCGM = new TCGM;
        $obTCGM->setDado('numcgm', $_REQUEST['cgmSocio']);
        $obTCGM->recuperaPorChave($rsCGM);

        $rsComprasTipoSocio = new Recordset;
        $obTComprasTipoSocio = new TComprasTipoSocio;
        $obTComprasTipoSocio->setDado('cod_tipo', $_REQUEST['inCodTipo']);
        $obTComprasTipoSocio->recuperaPorChave($rsComprasTipoSocio);
        
        $arSocioSessao[$inCount]['id']              = ($inCount+1);
        $arSocioSessao[$inCount]['cod_tipo']        = $rsComprasTipoSocio->getCampo('cod_tipo');
        $arSocioSessao[$inCount]['descricao']       = $rsComprasTipoSocio->getCampo('descricao');
        $arSocioSessao[$inCount]['numcgm']          = $rsCGM->getCampo('numcgm');
        $arSocioSessao[$inCount]['nom_cgm']         = $rsCGM->getCampo('nom_cgm');
        $arSocioSessao[$inCount]['ativo']           = $_REQUEST['boAtivo'];
        $arSocioSessao[$inCount]['ativo_descricao'] = $_REQUEST['boAtivo'] ? 'Sim':'Não';
        Sessao::write('arSocio' , $arSocioSessao);
        
        $stJs .= montaListaSocio( $arSocioSessao );

        echo $stJs;
    break;

    case 'excluirSocio':
        $inCount=0;
        $arSocioSessao = Sessao::read('arSocio');
        foreach ( $arSocioSessao as $arSocio ) {
            if ($arSocio['id'] != $_REQUEST['id']) {
                $arSocioSessaoAtualizado[$inCount]['id']              = $inCount;
                $arSocioSessaoAtualizado[$inCount]['cod_tipo']        = $arSocio['cod_tipo'];
                $arSocioSessaoAtualizado[$inCount]['descricao']       = $arSocio['descricao'];
                $arSocioSessaoAtualizado[$inCount]['numcgm']          = $arSocio['numcgm'];
                $arSocioSessaoAtualizado[$inCount]['nom_cgm']         = $arSocio['nom_cgm'];
                $arSocioSessaoAtualizado[$inCount]['ativo']           = $arSocio['ativo'];
                $arSocioSessaoAtualizado[$inCount]['ativo_descricao'] = $arSocio['ativo'] ? 'Sim':'Não';
                $inCount++;
            }
        }
        
        Sessao::write('arSocio' , array());
        Sessao::write('arSocio' , $arSocioSessaoAtualizado);
        $stJs .= montaListaSocio( $arSocioSessaoAtualizado );
        echo $stJs;
    break;

    case "incluirFornecedor":
        $boErro = false;
        $stMensagem = "";

        if (Sessao::read('arFornecedor')) {
            foreach ( Sessao::read('arFornecedor') as $arFornecedor ) {
                if ( trim($_REQUEST['inCodCatalogoTxt']."@%@".$_REQUEST['stChaveClassificacao']) ==  trim($arFornecedor['cod_catalogo']."@%@".$arFornecedor['classificacao']) ) {
                    $boErro = true;
                    $stMensagem = "Essa classificação já está na lista!()";
                }
            }
        }
        if ( empty( $_REQUEST["inCodClassificacao_1"] ) ) {
            $boErro = true;
            $stMensagem = "Selecione a classificação!()";
        }
        if (!$boErro) {
            $inCount = sizeof(Sessao::read('arFornecedor'));
            $catalogo = Sessao::read('catalogo');

            $arFornecedor = Sessao::read('arFornecedor');
            $arFornecedor[$inCount]['cgm_fornecedor'] = $_REQUEST['inCGM'];

            include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogo.class.php");
            $obTAlmoxarifadoCatalogo = new TAlmoxarifadoCatalogo;
            $obTAlmoxarifadoCatalogo->setDado("cod_catalogo",$_REQUEST['inCodCatalogo']);
            $obTAlmoxarifadoCatalogo->recuperaPorChave($rsRecordSetCatalogo);

            $arFornecedor[$inCount]['catalogo'      ] = $rsRecordSetCatalogo->getCampo("descricao");
            $arFornecedor[$inCount]['cod_catalogo'  ] = $_REQUEST['inCodCatalogo'];

            include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoNiveis.class.php" );
            $obTAlmoxarifadoCatalogoNiveis = new TAlmoxarifadoCatalogoNiveis;
            $obTAlmoxarifadoCatalogoNiveis->setDado ( 'codCatalogo', $_REQUEST['inCodCatalogo'] );
            $obTAlmoxarifadoCatalogoNiveis->recuperaMascaraCompleta ( $rsMascara );

            $stMascara = $rsMascara->getCampo( 'mascara' ) ;

            $stMascara =  str_replace (  '9', '0', $stMascara );
            $stMascara =  $_REQUEST['stChaveClassificacao'  ]   . substr( $stMascara, strlen ( $_REQUEST['stChaveClassificacao'  ])  );

            #sessao->transf3['arFornecedor'][$inCount]['classificacao'] = $stMascara;
            $arFornecedor[$inCount]['classificacao'] = $stMascara;

            $inNivelClassificacao = explode(".",$_REQUEST['stChaveClassificacao']);
            foreach ($inNivelClassificacao as $key)
                $i+=($key!="")?1:0;
            $inNivelClassificacao = $i;

            $inCodClassificacao = $_REQUEST['inCodClassificacao_'.$inNivelClassificacao.''];
            $inCodClassificacao = explode ('-',$inCodClassificacao);
            $arFornecedor[$inCount]['cod_classificacao'] = $inCodClassificacao[1];

            include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php");
            $obTAlmoxarifadoCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao;
            $obTAlmoxarifadoCatalogoClassificacao->setDado("cod_classificacao",$inCodClassificacao[1]);
            $obTAlmoxarifadoCatalogoClassificacao->recuperaPorChave($rsRecordSet);

            $arFornecedor[$inCount]['descricao'] = $rsRecordSet->getCampo("descricao");

            Sessao::write('arFornecedor' , $arFornecedor);
            $stJs .= montaListaFornecedor( $arFornecedor );
        } else {
            $stJs .= "alertaAviso( '$stMensagem','form','erro','".Sessao::getId()."' );";
        }
        echo $stJs;
    break;

    case 'excluirFornecedor':
        $arFornecedor = array();
        $inCount = 0;
        $key = trim($_REQUEST['cod_catalogo']."@%@".$_REQUEST['classificacao']);
        foreach ( Sessao::read('arFornecedor') as $value ) {
            $keyValue = trim($value['cod_catalogo']."@%@".$value['classificacao']);
            if ($key != $keyValue) {
                $arFornecedor[$inCount]['cgm_fornecedor'] = $value['cgm_fornecedor'];
                $arFornecedor[$inCount]['cod_classificacao'] = $value['cod_classificacao'];
                $arFornecedor[$inCount]['catalogo'  ] = $value['catalogo'  ];
                $arFornecedor[$inCount]['cod_catalogo'  ] = $value['cod_catalogo'  ];
                $arFornecedor[$inCount]['descricao'  ] = $value['descricao'  ];
                $arFornecedor[$inCount]['classificacao' ] = $value['classificacao' ];
                $inCount++;
            }
        }
        Sessao::write('arFornecedor' , array());
        Sessao::write('arFornecedor' , $arFornecedor);
        $stJs .= montaListaFornecedor( $arFornecedor );
        echo $stJs;
    break;

    case "incluirAtividade":
        //separa a chave e pega o código da atividade para recuperar o nome da atividade

        if ( !empty( $_REQUEST["inCodigoAtividade"] ) ) {

            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );
            $obTCEMAtividade = new TCEMAtividade();
            $obTCEMAtividade->setDado('cod_atividade' ,$_REQUEST['inCodigoAtividade'] );
            $obTCEMAtividade->recuperaPorChave($rsAtividades);
            $arAtividadesSessao = Sessao::read('arAtividades');
            if ($arAtividadesSessao) {
                foreach ($arAtividadesSessao as $arAtividades) {
                    //if ( trim($_REQUEST['stValorComposto']) == trim($arAtividades['codigo'] )) {
                    if ( trim($_REQUEST['inCodigoAtividade']) == trim($arAtividades['cod_atividade'] )) {
                        $boErro = true;
                        $stMensagem = "A atividade escolhida já está na lista!";
                    }
                }
            }

            if (!$boErro) {

                if ( intval(substr($_REQUEST['stValorComposto'],strrpos($_REQUEST['stValorComposto'],'.')+1, strlen($_REQUEST['stValorComposto']) ) ) < 0 ) {
                    $stValorComposto = substr($_REQUEST['stValorComposto'], 0, strrpos($_REQUEST['stValorComposto'], '.'));
                } else {
                    $stValorComposto = $_REQUEST['stValorComposto'];
                }

                $inCount = count($arAtividadesSessao);
                $arAtividadesSessao[$inCount]['cgm_fornecedor'] = $_REQUEST['inCGM'];
                $arAtividadesSessao[$inCount]['codigo'    ]  = $stValorComposto;
                $arAtividadesSessao[$inCount]['cod_atividade' ] = $_REQUEST['inCodigoAtividade'];
                $arAtividadesSessao[$inCount]['atividade'    ] = $rsAtividades->getCampo('nom_atividade');

                $stJs  = "f.inCodigoAtividade.value = '';\n";
                $stJs .= "d.getElementById('campoInner').innerHTML = '&nbsp;';\n";
                $stJs .= "d.getElementById('spnListaAtividade').innerHTML = ' ';    \n";
                $stJs .= "d.getElementById('spnDetalheAtividade').innerHTML = '';\n";

                Sessao::write('arAtividades', $arAtividadesSessao);
                $stJs .= montaListaAtividade ($arAtividadesSessao, false) ;

                echo $stJs;

            } else {
                $stJs .= "f.inCodigoAtividade.value = '';\n";
                $stJs .= "d.getElementById('campoInner').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso( '".$stMensagem."','form','erro','".Sessao::getId()."' );";
                $stJs .= "d.getElementById('spnDetalheAtividade').innerHTML = '';\n";
                echo $stJs;
            }
        } else {
            $stJs .= "alertaAviso( 'Selecione uma atividade!','form','erro','".Sessao::getId()."' );";
            echo $stJs;
        }
    break;

    case 'excluirAtividade':
        $arAtividades = array();
        $key = trim($_REQUEST['inCodAtividade']);
        foreach ( Sessao::read('arAtividades') as $value ) {
            $keyValue = trim($value['codigo']);
            if ($key != $keyValue) {
                $arAtividades[] = $value;
            }
        }
        Sessao::write('arAtividades' , $arAtividades);
        $stJs = montaListaAtividade( $arAtividades );
        echo $stJs;
    break;

    case "limparAtividade":
        $stJs  = "f.stChaveAtividade.value = '';";
        $stCombo  = "inCodAtividade_1";
        $stJs .= "f.$stCombo.selectedIndex = 0 ;\n";
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "preencheProxCombo":
        $obMontaAtividade = new MontaAtividade();
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );

        if ($_REQUEST["inPosicao"] == $_REQUEST["inNumNiveis"]) {
            $obMontaAtividade->setRetornaJs(true);
            $stJs .= $obMontaAtividade->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
            SistemaLegado::executaFrameOculto($stJs);
        } else {
            $obMontaAtividade->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
        echo $stJs;
    break;
    case "preencheCombosAtividade":
        $obMontaAtividade = new MontaAtividade();
        $obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaAtividade->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
        $obMontaAtividade->preencheCombosAtividade();
    break;

    case 'validaPadrao':
        foreach ($_REQUEST as $key => $value) {
            if ( preg_match( '/(^chkContaBanco_.*$)/',$key)) {
                $id = explode('_',$key);
                $id = $id[1];
                break;
            }
        }

        $arContaBancaria = array();
        $dadosConta = Sessao::read('arContaBancaria');
        foreach ($dadosConta as $key => $value) {
            $arContaBancaria[$key] = $value;
            if ( ($value['id_usuario']) == $id-1) {
                $stValorPadrao = $_REQUEST['chkContaBanco_'.$id];
                $arContaBancaria[$key]['padrao'] = $stValorPadrao == 1 ? false : true ;
            } else {
                $arContaBancaria[$key]['padrao'] = false;
            }
        }
        Sessao::write('arContaBancaria', array());
        Sessao::write('arContaBancaria', $arContaBancaria);
        $stJs = montaListaContaBancaria( $arContaBancaria );
        echo $stJs;
    break;

    case 'montaRecuperaFormulario':
        if (Sessao::read('arContaBancaria') != "")
            $stJs = montaListaContaBancaria( Sessao::read('arContaBancaria') );
        if (Sessao::read('arFornecedor') != "")
           $stJs .=montaListaFornecedor( Sessao::read('arFornecedor') );
        if (Sessao::read('arAtividades') != "")
            $stJs .= montaListaAtividade (Sessao::read('arAtividades'),false) ;
        if (Sessao::read('arSocio') != "")
            $stJs .= montaListaSocio (Sessao::read('arSocio')) ;
            
        $stJs .= retornaPisPasep();
        echo $stJs;
    break;

    case 'limpaFormulario':
        Sessao::write('arAtividades' , array());
        Sessao::write('arFornecedor' ,  array());
        Sessao::write('arContaBancaria' , array());
    break;

    case 'buscaAtividade':
        // preenchendo o detalhamento da atividade
        if ($_REQUEST['inCodigoAtividade']) {
            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );
            $obTCEMAtividade = new TCEMAtividade();
            $arValorComposto = explode('.', $_REQUEST['stValorComposto']);

            $obFormulario = new Formulario();

            for ( $i=0; $i<count($arValorComposto); $i++ ) {

                if ( intval($arValorComposto[$i]) <= 0 ) {
                    break;
                }

                if ( $i+1 == count($arValorComposto) ) {
                    $stDado .= $arValorComposto[$i];
                    $obTCEMAtividade->setDado( 'cod_estrutural', $stDado );
                } else {
                    $stDado .= $arValorComposto[$i].'.';
                    $obTCEMAtividade->setDado( 'cod_estrutural', $stDado.'%' );
                }

                $obTCEMAtividade->recuperaAtividadePorEstrutural( $rsAtividade );

                $obLabel = 'obLabel'.$i;

                $$obLabel = new Label();

                if ( ($i+1) < count($arValorComposto) ) {
                    $stDadoAux = substr($stDado, 0, strlen($stDado)-1);
                } else {
                    $stDadoAux = $stDado;
                }

                $$obLabel->setValue( $stDadoAux .' - '. $rsAtividade->getCampo('nom_atividade') );
                $$obLabel->setId   ( 'stAtividade'.$i );
                $$obLabel->setRotulo( '&nbsp;' );

                $obFormulario->addComponente($$obLabel);
            }
            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnDetalheAtividade').innerHTML = '".$obFormulario->getHTML()."';\n";
        } else {
            $stJs = "jq('#campoInner').html('&nbsp;');\n";
            $stJs .= "d.getElementById('spnDetalheAtividade').innerHTML = '&nbsp';\n";
        }
        echo $stJs;
    break;

    case 'preencheInnerAtividade':
        if ($_REQUEST['inCodigoAtividade']) {

            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );
            $obTCEMAtividade = new TCEMAtividade();
            $obTCEMAtividade->setDado( 'cod_atividade', $_REQUEST['inCodigoAtividade'] );
            $obTCEMAtividade->recuperaPorChave( $rsAtividade );

            if ( $rsAtividade->getNumLinhas() > 0 ) {
                $stJs  = "d.getElementById('campoInner').innerHTML = '".$rsAtividade->getCampo( 'nom_atividade' )."';\n";
                $stJs .= "f.stValorComposto.value = '".$rsAtividade->getCampo( 'cod_estrutural' )."';\n";
                $stJs .= "montaParametrosGET('buscaAtividade', 'inCodigoAtividade, stValorComposto');\n";
            } else {
                $stJs  = "d.getElementById('campoInner').innerHTML = '&nbsp;';\n";
                $stJs .= "f.stValorComposto.value = '';\n";
                $stJs .= "f.inCodigoAtividade.value = '';\n";
                $stJs .= "f.inCodigoAtividade.focus();\n";
            }
        } else {
            $stJs  = "jq('#campoInner').html('&nbsp;');\n";
        }

        echo $stJs;
    break;

    case 'alteraContaBancaria' :

        $stJs = alterarContaBancaria( $_REQUEST['banco'], $_REQUEST['agencia'], $_REQUEST['conta'] );
        echo $stJs;
    break;

    case 'alterarContaBancaria':
        $stErro = true;
        $nuCont = 0;

        $arContaBancaria = Sessao::read('arContaBancaria');
        if ($arContaBancaria) {
            foreach ($arContaBancaria as $key => $arDadosConta) {
                if ($arDadosConta['banco'].$arDadosConta['agencia'].$arDadosConta['conta'] == $_REQUEST['stChaveConta']) {
                    $nuCont++;
                    $nuChave = $key;
                }
            }
        }
        if ($nuCont > 1) {
            $stJs .= "alertaAviso( 'A conta escolhida já está na lista!','form','erro','".Sessao::getId()."' );";
        } else {
            include_once TMON."TMONBanco.class.php";
            $obTMONBanco = new TMONBanco() ;
            $stFiltro = " WHERE  num_banco = '".$_REQUEST['inCodBancoTxt']."' ";
            $obTMONBanco->recuperaTodos( $rsBanco, $stFiltro );

            include_once TMON."TMONAgencia.class.php";
            $obTMONAgencia = new TMONAgencia() ;
            $stFiltro = " WHERE cod_banco = '".$rsBanco->getCampo('cod_banco')."'
                               AND num_agencia = '".$_REQUEST['stNumAgencia']."' " ;

            $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);

            $arContaBancaria[$nuChave]['banco']      = $_REQUEST['inCodBancoTxt' ];
            $arContaBancaria[$nuChave]['cod_banco']  = $rsBanco->getCampo('cod_banco');
            $arContaBancaria[$nuChave]['nom_banco']  = $rsBanco->getCampo('nom_banco');

            $arContaBancaria[$nuChave]['agencia']      = $_REQUEST['stNumAgencia'];
            $arContaBancaria[$nuChave]['cod_agencia']  = $rsAgencia->getCampo('cod_agencia');

            $arContaBancaria[$nuChave]['conta']       = $_REQUEST['stContaCorrente'];
            $arContaBancaria[$nuChave]['nom_agencia'] = $rsAgencia->getCampo('nom_agencia');

            Sessao::write('arContaBancaria' , $arContaBancaria);
            $stJs .= montaListaContaBancaria( $arContaBancaria );
            $stJs .= "jq('#stContaCorrente').val('');";

        }
        echo $stJs;

    break;

    case "buscaPisPasep":
        echo retornaPisPasep();
    break;

}
//echo $stJs;
?>
