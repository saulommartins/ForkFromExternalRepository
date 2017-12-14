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
  * Página Oculta para gerar o configuracao RGF e RREO
  * Data de Criação:  05/05/2016

  * @author Analista:Ane Caroline Pereira
  * @author Desenvolvedor: Lisiane Morais
  *
  *$Id: OCManterConfiguracaoRgfRreo.php 65345 2016-05-13 18:07:34Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';


switch ($request->get('stCtrl')) {
    case 'incluirListaVeiculosRGF':
        $js .= incluirListaVeiculo('RGF');
    break;

    case 'incluirListaVeiculosRREO':
        $js .= incluirListaVeiculo('RREO');
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculosRGF':
        alterarListaVeiculos('RGF');
    break;

    case 'alterarListaVeiculosRREO':
        alterarListaVeiculos('RREO');
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculosRGF":
        alteradoListaVeiculos('RGF');
    break;

    case "alteradoListaVeiculosRREO":
        alteradoListaVeiculos('RREO');
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculosRGF':
        excluirListaVeiculos('RGF');
    break;

    case 'excluirListaVeiculosRREO':
        excluirListaVeiculos('RREO');
    break;

    case 'limparVeiculoRGF' :
        limparVeiculo('RGF');
    break;

    case 'limparVeiculoRREO' :
        limparVeiculo('RREO');
    break;

    case 'carregaListaVeiculosRGF' :
        $arValores = Sessao::read('arValoresRGF');
        echo montaListaVeiculos( 'RGF', $arValores, true, $request->get('consultar'));
    break;

    case 'carregaListaVeiculosRREO' :
        $arValores = Sessao::read('arValoresRREO');
        echo montaListaVeiculos( 'RREO', $arValores, true, $request->get('consultar'));
    break;
}

function incluirListaVeiculo($stTipo) {
    global $request; 

    if($stTipo == 'RGF') {
        $arValores = Sessao::read('arValoresRGF');
    }elseif($stTipo == 'RREO') {
        $arValores = Sessao::read('arValoresRREO');
    }

    if ($request->get('inVeiculo'.$stTipo) == '') {
        $stMensagem = 'Preencha o campo Veículo de Publicação!';
    }

    if ($request->get('dtDataPublicacao'.$stTipo) == '') {
        $stMensagem = 'Preencha o campo Data de Publicação!';
    }

    $boPublicacaoRepetida = false;
    if ( is_array( $arValores ) ) {
        foreach ($arValores as $arTEMP) {
            if ($arTEMP['inVeiculo'] == $request->get("inVeiculo".$stTipo) & $arTEMP['dtDataPublicacao'] == $request->get('dtDataPublicacao'.$stTipo)) {
                $boPublicacaoRepetida = true ;
                $stMensagem = "Este veículos de publicação já está na lista.";
            }
        }
    }

    if (!$boPublicacaoRepetida AND !$stMensagem) {
        $inCount = sizeof($arValores);
        $arValores[$inCount]['id'               ] = $inCount + 1;
        $arValores[$inCount]['inVeiculo'        ] = $request->get( "inVeiculo".$stTipo                  );
        $arValores[$inCount]['stVeiculo'        ] = $request->get( "stNomCgmVeiculoPublicadade".$stTipo );
        $arValores[$inCount]['dtDataPublicacao' ] = $request->get( "dtDataPublicacao".$stTipo           );
        $arValores[$inCount]['inNumPublicacao'  ] = $request->get( "inNumPublicacao".$stTipo            );
        $arValores[$inCount]['stObservacao'     ] = $request->get( "stObservacao".$stTipo               );
    } else {
        echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    if($stTipo == 'RGF') {
        Sessao::write('arValoresRGF', $arValores);
    }elseif($stTipo == 'RREO') {
        Sessao::write('arValoresRREO', $arValores);
    }

    $stJs =montaListaVeiculos( $stTipo, $arValores, true, $request->get('consultar'));
    $stJs.="$('HdnCodVeiculo').value ='';                                    \n";
    $stJs.="$('inVeiculo".$stTipo."').value ='';                             \n";
    $stJs.="$('dtDataPublicacao".$stTipo."').value ='".date('d/m/Y')."';     \n";
    $stJs.="$('inNumPublicacao".$stTipo."').value ='';                       \n";
    $stJs.="$('stObservacao".$stTipo."').value = '';                         \n";
    $stJs.="$('stNomCgmVeiculoPublicadade".$stTipo."').innerHTML = '&nbsp;'; \n";
    $stJs.="$('incluiVeiculo".$stTipo."').value = 'Incluir';                 \n";
    $stJs.="$('incluiVeiculo".$stTipo."').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos".$stTipo."\', \'id, inVeiculo".$stTipo.", stVeiculo".$stTipo.", dtDataPublicacao".$stTipo.", inNumPublicacao".$stTipo.",stNomCgmVeiculoPublicadade".$stTipo.", stObservacao".$stTipo.", HdnCodVeiculo\')');";

    echo $stJs;
}

function montaListaVeiculos( $stTipo, $arRecordSet , $boExecuta = true, $stConsultar=null){
    if (is_array($arRecordSet)) {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

        $table = new Table();
        $table->setRecordset   ( $rsRecordSet  );
        $table->setSummary     ( "Listas de Publicação ".$stTipo );

        $table->Head->addCabecalho( 'Veículo de Publicação' , 40 );
        $table->Head->addCabecalho( 'Data'                  , 8  );
        $table->Head->addCabecalho( 'Número Publicação'     , 12 );
        $table->Head->addCabecalho( 'Observação'            , 40 );

        $table->Body->addCampo( '[inVeiculo]-[stVeiculo] ' , 'E' );
        $table->Body->addCampo( 'dtDataPublicacao'         , 'C' );
        $table->Body->addCampo( 'inNumPublicacao' );
        $table->Body->addCampo( 'stObservacao'    );

        if(!$stConsultar) {
            $table->Body->addAcao( 'alterar', 'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )', array( "alterarListaVeiculos".$stTipo, 'id' ) );
            $table->Body->addAcao( 'excluir', 'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )', array( "excluirListaVeiculos".$stTipo, 'id' ) );
        }

        $table->montaHTML( true );

        if ($boExecuta) {
            return "d.getElementById('spnListaVeiculos".$stTipo."').innerHTML = '".$table->getHTML()."';";
        } else {
            return $this->getHTML();
        }
    }
}

function alterarListaVeiculos($stTipo) {
    global $request; 

    if($stTipo == 'RGF') {
        $arValores = Sessao::read('arValoresRGF');
    }elseif($stTipo == 'RREO') {
        $arValores = Sessao::read('arValoresRREO');
    }

    if ( is_array($arValores)) {
        foreach ($arValores as $key => $value) {
            if (($key+1) == $request->get('id')) {
                $js ="$('HdnCodVeiculo').value                                 ='".$request->get('id')."';                  \n";
                $js.="$('inVeiculo".$stTipo."').value                          ='".$arValores[$key]['inVeiculo']."';        \n";
                $js.="$('dtDataPublicacao".$stTipo."').value                   ='".$arValores[$key]['dtDataPublicacao']."'; \n";
                $js.="$('inNumPublicacao".$stTipo."').value                    ='".$arValores[$key]['inNumPublicacao']."';  \n";
                $js.="$('stObservacao".$stTipo."').value                       ='".$arValores[$key]['stObservacao']."';     \n";
                $js.="$('stNomCgmVeiculoPublicadade".$stTipo."').innerHTML     ='".$arValores[$key]['stVeiculo']."';        \n";
                $js.="$('incluiVeiculo".$stTipo."').value                      ='Alterar';                                  \n";
                $js.="$('incluiVeiculo".$stTipo."').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos".$stTipo."\', \'id, inVeiculo".$stTipo.", stVeiculo".$stTipo.", dtDataPublicacao".$stTipo.", inNumPublicacao".$stTipo.",stNomCgmVeiculoPublicadade".$stTipo.", stObservacao".$stTipo.", HdnCodVeiculo\')');";
                $js.="$('inVeiculo".$stTipo."').focus();                                                                    \n";

                break;
            }
        }
    }
    echo $js;
}

function alteradoListaVeiculos($stTipo){
    global $request; 
    $inCount = 0;

    $boPublicacaoRepetida = false;
    if($stTipo == 'RGF') {
        $arValores = Sessao::read('arValoresRGF');
    }elseif($stTipo == 'RREO') {
        $arValores = Sessao::read('arValoresRREO');
    }
    
    if ($request->get('inVeiculo'.$stTipo) == '') {
        $stMensagem = 'Preencha o campo Veículo de Publicação!';
    }

    if ($request->get('dtDataPublicacao'.$stTipo) == '') {
        $stMensagem = 'Preencha o campo Data de Publicação!';
    }

    if (!$boPublicacaoRepetida && !$stMensagem) {
        foreach ($arValores as $key=>$value) {
            if (($key+1) == $request->get('HdnCodVeiculo')) {
                $arValores[$inCount]['id'               ] = $inCount + 1;
                $arValores[$inCount]['inVeiculo'        ] = $request->get( "inVeiculo".$stTipo );
                $arValores[$inCount]['stVeiculo'        ] = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$request->get( "inVeiculo".$stTipo ).' ');
                $arValores[$inCount]['dtDataPublicacao' ] = $request->get( "dtDataPublicacao".$stTipo );
                $arValores[$inCount]['inNumPublicacao'  ] = $request->get( "inNumPublicacao".$stTipo );
                $arValores[$inCount]['stObservacao'     ] = $request->get( "stObservacao".$stTipo );
            }
            $inCount++;
        }

        if($stTipo == 'RGF') {
            Sessao::write('arValoresRGF', $arValores);
        }elseif($stTipo == 'RREO') {
            Sessao::write('arValoresRREO', $arValores);
        }

        $stJs =montaListaVeiculos( $stTipo, $arValores, true, $request->get('consultar'));
        $stJs.="$('HdnCodVeiculo').value ='';                                    \n";
        $stJs.="$('inVeiculo".$stTipo."').value ='';                             \n";
        $stJs.="$('dtDataPublicacao".$stTipo."').value ='".date('d/m/Y')."';     \n";
        $stJs.="$('inNumPublicacao".$stTipo."').value ='';                       \n";
        $stJs.="$('stObservacao".$stTipo."').value = '';                         \n";
        $stJs.="$('stNomCgmVeiculoPublicadade".$stTipo."').innerHTML = '&nbsp;'; \n";
        $stJs.="$('incluiVeiculo".$stTipo."').value = 'Incluir';                 \n";
        $stJs.="$('incluiVeiculo".$stTipo."').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos".$stTipo."\', \'id, inVeiculo".$stTipo.", stVeiculo".$stTipo.", dtDataPublicacao".$stTipo.", inNumPublicacao".$stTipo.",stNomCgmVeiculoPublicadade".$stTipo.", stObservacao".$stTipo.", HdnCodVeiculo\')');";

        echo $stJs;
    } else {
        if($stMensagem)
            echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        else
            echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
    }
}

function excluirListaVeiculos($stTipo){
    global $request; 
    $arTEMP            = array();
    $inCount           = 0;

    if($stTipo == 'RGF') {
        $arValores = Sessao::read('arValoresRGF');
    }elseif($stTipo == 'RREO') {
        $arValores = Sessao::read('arValoresRREO');
    }

    foreach ($arValores as $key => $value) {
        if (($key+1) != $request->get('id')) {
            $arTEMP[$inCount]['id'               ] = $inCount + 1;
            $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"        ];
            $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"        ];
            $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
            $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao"  ];
            $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"     ];
            $arTEMP[$inCount]['inCodLicitacao'   ] = $value[ "inCodLicitacao"   ];
            $inCount++;
        }
    }

    if($stTipo == 'RGF') {
        Sessao::write('arValoresRGF', $arTEMP);
    }elseif($stTipo == 'RREO') {
        Sessao::write('arValoresRREO', $arTEMP);
    }
    Sessao::write('arValores', $arTEMP);

    echo montaListaVeiculos( $stTipo, $arTEMP, true, $request->get('consultar') );
}

function limparVeiculo($stTipo) {
    $stJs ="$('HdnCodVeiculo').value ='';                                    \n";
    $stJs.="$('inVeiculo".$stTipo."').value ='';                             \n";
    $stJs.="$('dtDataPublicacao".$stTipo."').value ='".date('d/m/Y')."';     \n";
    $stJs.="$('inNumPublicacao".$stTipo."').value ='';                       \n";
    $stJs.="$('stObservacao".$stTipo."').value = '';                         \n";
    $stJs.="$('stNomCgmVeiculoPublicadade".$stTipo."').innerHTML = '&nbsp;'; \n";
    $stJs.="$('incluiVeiculo".$stTipo."').value = 'Incluir';                 \n";
    $stJs.="$('incluiVeiculo".$stTipo."').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos".$stTipo."\', \'id, inVeiculo".$stTipo.", stVeiculo".$stTipo.", dtDataPublicacao".$stTipo.", inNumPublicacao".$stTipo.",stNomCgmVeiculoPublicadade".$stTipo.", stObservacao".$stTipo.", HdnCodVeiculo\')');";

    echo $stJs;
}

if($stJs) {
    echo $stJs;
}

?>