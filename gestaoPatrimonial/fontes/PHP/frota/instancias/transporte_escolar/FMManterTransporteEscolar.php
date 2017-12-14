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
    * Data de Criação: 11/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: FMManterTransporteEscolar.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTurno.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTransporteEscolar.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransporteEscolar";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

SistemaLegado::LiberaFrames(true,false);

$stAcao   = $request->get('stAcao');
$stModulo = $request->get('modulo');

$stLocation = $pgFilt . "?". Sessao::getId() . "&stAcao=" . $stAcao;

// Receber codVeiculo e CgmEscola para montar Array de informações dos mesmos
$inCodVeiculo = $_REQUEST['inCodVeiculo'];
$inCgmEscola = $_REQUEST['inCgmEscola'];

$arDados[0] = array('cod_veiculo'=> '','st_marca'=> '', 'st_modelo'=> '', 'st_escola'=> '');


//Define Hidden cod_veiculo
$obHdnCodVeiculo = new Hidden;
$obHdnCodVeiculo->setName ( "inCodVeiculo" );
$obHdnCodVeiculo->setValue( $inCodVeiculo );

//Define Hidden cod_veiculo
$obHdnCgmEscola = new Hidden;
$obHdnCgmEscola->setName ( "inCgmEscola" );
$obHdnCgmEscola->setValue( $inCgmEscola );

if($inCodVeiculo!=''&&$inCgmEscola!=''){
    $obTFrotaVeiculo = new TFrotaVeiculo;
    $stFiltro = "AND veiculo.cod_veiculo=".$inCodVeiculo." ";
    $obTFrotaVeiculo->recuperaVeiculoSintetico($rsListaVeiculos,$stFiltro,$stOrder,$boTransacao);
    
    $where = " WHERE numcgm=".$inCgmEscola;
    $nomEscola = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);

    if ( $rsListaVeiculos->getNumLinhas() == 1 && $nomEscola!='' && $nomEscola!=null ) {
        $arDados[0]['cod_veiculo'] = $rsListaVeiculos->getCampo('cod_veiculo');
        $arDados[0]['st_marca'] = $rsListaVeiculos->getCampo('nom_marca');
        $arDados[0]['st_modelo'] = $rsListaVeiculos->getCampo('nom_modelo');
        $arDados[0]['st_escola'] = $inCgmEscola." - ".$nomEscola;
    }
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setName('frm');

//Define o objeto da ação stAcao
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "stModulo" );
$obHdnModulo->setValue( $stModulo );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

/*
 * Monta Lista de dados de Veiculo e Escola
*/
$obTxtCodVeiculo = new Label;
$obTxtCodVeiculo->setName   ( "nuCodVeiculo"                );
$obTxtCodVeiculo->setId     ( "nuCodVeiculo"                );
$obTxtCodVeiculo->setRotulo ( "Código do Veículo"           );
$obTxtCodVeiculo->setValue  ( $arDados[0]['cod_veiculo']    );

$obTxtMarca = new Label;
$obTxtMarca->setName    ( "stMarca"                 );
$obTxtMarca->setId      ( "stMarca"                 );
$obTxtMarca->setRotulo  ( "Marca"                   );
$obTxtMarca->setValue   ( $arDados[0]['st_marca']   );

$obTxtModelo = new Label;
$obTxtModelo->setName   ( "stModelo"                );
$obTxtModelo->setId     ( "stModelo"                );
$obTxtModelo->setRotulo ( "Modelo"                  );
$obTxtModelo->setValue  ( $arDados[0]['st_modelo']  );

$obTxtEscola = new Label;
$obTxtEscola->setName   ( "stEscola"                );
$obTxtEscola->setId     ( "stEscola"                );
$obTxtEscola->setRotulo ( "CGM Escola"              );
$obTxtEscola->setValue  ( $arDados[0]['st_escola']  );


/*
 * Monta Lista de dados do Mês
*/
$obTFrotaTransporteEscolar = new TFrotaTransporteEscolar();

//Verificar se é Alteração ou Inclusão
$stFiltro  = " WHERE cod_veiculo=".$inCodVeiculo;
$stFiltro .= " AND cgm_escola=".$inCgmEscola;
$stFiltro .= " AND exercicio='".Sessao::getExercicio()."'";
$obTFrotaTransporteEscolar->recuperaTodos($rsTransporte, $stFiltro);

$stMes = array  (1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho',
                7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro',11=>'Novembro', 12=>'Dezembro');

$count = 0; 
for ($i=1;$i<13;$i++) {
    $arRecordSet[$count]['mes'] = $stMes[$i];
    $arRecordSet[$count]['cod_mes'] = $i;
    
    for($a=0;$a<$rsTransporte->getNumLinhas();$a++){
        if($rsTransporte->arElementos[$a]['mes']==$i){
            $arRecordSet[$count]['passageiros'] = $rsTransporte->arElementos[$a]['passageiros'];
            $arRecordSet[$count]['distancia']   = $rsTransporte->arElementos[$a]['distancia'];
            $arRecordSet[$count]['dias_rodados']= $rsTransporte->arElementos[$a]['dias_rodados'];
            $arRecordSet[$count]['cod_turno_ant']   = $rsTransporte->arElementos[$a]['cod_turno'];
        }
    }
    
    $count++;
}
$rsRecordSet = new RecordSet();
$rsRecordSet->preenche( $arRecordSet );

$obTFrotaTurno = new TFrotaTurno();
$obTFrotaTurno->recuperaTodos($rsTurno);

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Informações do Mês');
$obLista->setRecordSet($rsRecordSet);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Mês', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[mes]');
$obLista->commitDado();

$obTxtPassageiros = new TextBox();
$obTxtPassageiros->setName      ('inPassageiros_[cod_mes]');
$obTxtPassageiros->setSize      ( 10 );
$obTxtPassageiros->setMaxLength ( 5 );
$obTxtPassageiros->setValue     ('[passageiros]');
$obTxtPassageiros->setInteiro   ( true  );

$obLista->addCabecalho('Passageiros', 4);
$obLista->addDadoComponente( $obTxtPassageiros , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obTxtDistancia = new TextBox();
$obTxtDistancia->setName        ('inDistancia_[cod_mes]');
$obTxtDistancia->setSize        ( 10 );
$obTxtDistancia->setMaxLength   ( 11 );
$obTxtDistancia->setValue       ('[distancia]');
$obTxtDistancia->setInteiro     ( true  );

$obLista->addCabecalho('Distância', 4);
$obLista->addDadoComponente( $obTxtDistancia , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obTxtDias = new TextBox();
$obTxtDias->setName         ('inDias_[cod_mes]');
$obTxtDias->setSize         ( 10 );
$obTxtDias->setMaxLength    ( 2 );
$obTxtDias->setValue        ('[dias_rodados]');
$obTxtDias->setInteiro      ( true  );

$obLista->addCabecalho('Dias Rodados', 4);
$obLista->addDadoComponente( $obTxtDias , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

$obCmbTurno = new Select();
$obCmbTurno->setName( 'inNumTurno_[cod_mes]' );
$obCmbTurno->setId( 'inNumTurno_[cod_mes]' );
$obCmbTurno->setValue( '[cod_turno_ant]' );
$obCmbTurno->addOption( '', 'Selecione' );
$obCmbTurno->setCampoId( '[cod_turno]' );
$obCmbTurno->setCampoDesc( 'descricao' );
$obCmbTurno->setStyle('width:250px;');
$obCmbTurno->preencheCombo( $rsTurno );

$obLista->addCabecalho('Turno', 5);
$obLista->addDadoComponente( $obCmbTurno , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

// Define a Lista de Mês
$obSpnMes = new Span();
$obSpnMes->setId('spnMes');
$obLista->montaHTML();
$obSpnMes->setValue($obLista->getHTML());

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm           );
$obFormulario->addHidden( $obHdnCtrl        );
$obFormulario->addHidden( $obHdnModulo      );
$obFormulario->addHidden( $obHdnAcao        );
$obFormulario->addHidden( $obHdnCodVeiculo  );
$obFormulario->addHidden( $obHdnCgmEscola   );
$obFormulario->addTitulo( "Dados do Veículo e Escola"   );
$obFormulario->addComponente( $obTxtCodVeiculo          );
$obFormulario->addComponente( $obTxtMarca               );
$obFormulario->addComponente( $obTxtModelo              );
$obFormulario->addComponente( $obTxtEscola              );
$obFormulario->addSpan  ( $obSpnMes     );

if ($stAcao=="incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $stLocation );
}

//$obFormulario->OK();
//$obFormulario->Cancelar( $stLocation );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
