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
* Página de Aba de Identificação
* Data de Criação   : ???

* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 30921 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once($pgOculIdentificacao);

if( (integer)substr(sprintf('%o', fileperms(CAM_GRH_PES_ANEXOS)), -4) >= 755) {
    if( (integer)substr(sprintf('%o', fileperms(CAM_GRH_PES_ANEXOS.$stNomeFoto)), -4) >= 644) {
        $obImgFoto = new Img;
        $obImgFoto->setRotulo           ( "Foto" 	);
        $obImgFoto->setId               ( "stFoto" 	);
        $obImgFoto->setNull             ( true		);
        
        if ($stNomeFoto!='no_foto.jpeg' and $stNomeFoto) {
            $tam = getimagesize(CAM_GRH_PES_ANEXOS.$stNomeFoto);
            $tam_x = $tam[0];
            $tam_y = $tam[1];
            //DEFINE OS PARÂMETROS DA MINIATURA
            if ($tam_x > 85 || $tam_y > 112) {
                if ( ($tam_x-85)/3 > ($tam_y - 112)/4 ) {
                    //DIMINUI PELO X
                    $porcentagem = 85 / $tam_x;
                    $largura = round($tam_x * $porcentagem);
                    $altura = round($tam_y * $porcentagem);
                } else {
                    //DIMINUI PELO Y
                    $porcentagem = 112 / $tam_y;
                    $largura = round($tam_x * $porcentagem);
                    $altura = round($tam_y * $porcentagem);
                }
            } else {
                $largura = $tam_x;
                $altura = $tam_y;
            }
            $obImgFoto->setWidth            ( $largura  );
            $obImgFoto->setHeight           ( $altura  );
            $obImgFoto->setCaminho       	( CAM_GRH_PES_ANEXOS.$stNomeFoto);
        } else {
            $obImgFoto->setWidth            ( "60" );
            $obImgFoto->setHeight           ( "80"	);
            $obImgFoto->setCaminho       	( CAM_FW_IMAGENS."no_foto.jpeg" );
        }
    } else {
        $obLblFoto = new Label();
        $obLblFoto->setRotulo("Fotos");
        $obLblFoto->setValue("Falha ao carregar foto, problema de permissão. Contactar administrador do sistema.");
    }
} else {
    $obLblFoto = new Label();
    $obLblFoto->setRotulo("Fotos");
    $obLblFoto->setValue("Falha ao carregar foto, problema de permissão. Contactar administrador do sistema.");
}
$obFilFoto = new FileBox;
$obFilFoto->setRotulo        ( "Caminho" );
$obFilFoto->setName          ( "stCaminhoFoto" );
$obFilFoto->setId            ( "stCaminhoFoto" );
$obFilFoto->setSize          ( 40 );
$obFilFoto->setNull          ( true );
$obFilFoto->setMaxLength     ( 100 );

$obBtnFoto = new Button;
$obBtnFoto->setName  ( "Incluir" );
$obBtnFoto->setValue ( "Incluir" );
$obBtnFoto->setStyle ( "width: 80px" );
$obBtnFoto->obEvento->setOnClick("incluirFoto();");

$obBtnFotoExcluir = new Button;
$obBtnFotoExcluir->setName  ( "Remover" );
$obBtnFotoExcluir->setValue ( "Remover" );
$obBtnFotoExcluir->setStyle ( "width: 80px" );
$obBtnFotoExcluir->obEvento->setOnClick("excluirFoto();");

$obFormularioFoto = new Formulario;
if( (integer)substr(sprintf('%o', fileperms(CAM_GRH_PES_ANEXOS)), -4) >= 755) {
if( (integer)substr(sprintf('%o', fileperms(CAM_GRH_PES_ANEXOS.$stNomeFoto)), -4) >= 644)
    $obFormularioFoto->addComponente( $obImgFoto );
else
    $obFormularioFoto->addComponente( $obLblFoto );
} else
    $obFormularioFoto->addComponente( $obLblFoto );
    
if ($stNomeFoto != 'no_foto.jpeg' and $stNomeFoto) {
    $obFormularioFoto->agrupaComponentes( array($obFilFoto,$obBtnFoto,$obBtnFotoExcluir) );
} else {
    $obFormularioFoto->agrupaComponentes( array($obFilFoto,$obBtnFoto) );
}
$obFormularioFoto->montaInnerHTML();

$obSpnFoto = new Span;
$obSpnFoto->setId    ( "spnFoto"                   );
$obSpnFoto->setValue ( $obFormularioFoto->getHTML() );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( "inNumCGM" );
$obHdnCGM->setValue( $inNumCGM );

$obLblCGM = new Label;
$obLblCGM->setRotulo( "CGM"      );
$obLblCGM->setValue ( $inNumCGM."-".$stNomCGM  );
$obLblCGM->setId    ( "inNomCGM" );

$obLblDataNascimento = new Label;
$obLblDataNascimento->setRotulo ( "Data de Nascimento" );
$obLblDataNascimento->setValue  ( $stDataNascimento    );
$obLblDataNascimento->setId     ( "stDataNascimento"   );

$obHdnDataNascimento = new Hidden;
$obHdnDataNascimento->setName( "dtDataNascimento" );
$obHdnDataNascimento->setValue( $stDataNascimento );

$obDtaNascimento = new Data;
$obDtaNascimento->setName   ( 'stDataNascimento'                                     );
$obDtaNascimento->setValue  ( $request->get('stDataNascimento')                      );
$obDtaNascimento->setTitle  ( 'Informe a data de nascimento do servidor.'            );
$obDtaNascimento->setNull   ( false                                                  );
$obDtaNascimento->setRotulo ( 'Data de Nascimento'                                   );

$obLblSexo = new Label;
$obLblSexo->setRotulo ( "Sexo"   );
$obLblSexo->setValue  ( $stSexo  );
$obLblSexo->setId     ( "stSexo" );

$obTxtPai = new TextBox;
$obTxtPai->setRotulo           ( "Nome do Pai"                        );
$obTxtPai->setTitle            ( "Informe o nome do pai do servidor." );
$obTxtPai->setValue            ( $stNomePai                           );
$obTxtPai->setName             ( "stNomePai"                          );
$obTxtPai->setSize             ( 40                                   );
$obTxtPai->setMaxLength        ( 80                                   );

$obTxtMae = new TextBox;
$obTxtMae->setRotulo           ( "Nome da Mãe"                        );
$obTxtMae->setTitle            ( "Informe o nome do mãe do servidor." );
$obTxtMae->setValue            ( $stNomeMae                           );
$obTxtMae->setName             ( "stNomeMae"                          );
$obTxtMae->setSize             ( 40                                   );
$obTxtMae->setMaxLength        ( 80                                   );
$obTxtMae->setNull             ( false                                );

$obRPessoalServidor->recuperaTodosEstadoCivil( $rsEstadoCivil );
$obTxtCodEstadoCivil = new TextBox;
$obTxtCodEstadoCivil->setName      ( "inCodEstadoCivil"                     );
$obTxtCodEstadoCivil->setRotulo    ( "Estado Civil"                         );
$obTxtCodEstadoCivil->setTitle     ( "Selecione o estado civil do servidor." );
$obTxtCodEstadoCivil->setValue     ( $inCodEstadoCivil                      );
$obTxtCodEstadoCivil->setMaxLength ( 1                                      );
$obTxtCodEstadoCivil->setSize      ( 10                                     );
$obTxtCodEstadoCivil->setNull      ( false                                  );
$obTxtCodEstadoCivil->setInteiro   ( true                                   );

$obCmbEstadoCivil = new Select;
$obCmbEstadoCivil->setName       ( "stEstadoCivil"                        );
$obCmbEstadoCivil->setRotulo     ( "Estado Civil"                         );
$obCmbEstadoCivil->setTitle      ( "Selecione o estado civil do servidor." );
$obCmbEstadoCivil->setStyle      ( "width: 250px"                         );
$obCmbEstadoCivil->setValue      ( $inCodEstadoCivil                      );
$obCmbEstadoCivil->setNull       ( false                                  );
$obCmbEstadoCivil->addOption     ( "", "Selecione"                        );
$obCmbEstadoCivil->setCampoID    ( "[cod_estado]"                         );
$obCmbEstadoCivil->setCampoDesc  ( "[nom_estado]"                         );
$obCmbEstadoCivil->preencheCombo ( $rsEstadoCivil                         );

$obBscCGMConjuge = new IPopUpCGM($obForm);
$obBscCGMConjuge->setId                   ( 'stNomConjuge');
$obBscCGMConjuge->setRotulo               ( 'CGM do Cônjuge'       );
$obBscCGMConjuge->setTipo                 ('fisica'           );
$obBscCGMConjuge->setTitle                ( 'Informe o CGM do cônjuge do servidor.');
$obBscCGMConjuge->setValue                ( $stNomConjuge );
$obBscCGMConjuge->setNull                 ( true );
$obBscCGMConjuge->obCampoCod->setName     ( 'inCGMConjuge' );
$obBscCGMConjuge->obCampoCod->setSize     (10);
$obBscCGMConjuge->obCampoCod->setValue    ( $inCGMConjuge   );

$obRPessoalServidor->recuperaTodosRaca( $rsRaca );
$obTxtCodRaca = new TextBox;
$obTxtCodRaca->setName      ( "inCodRaca"                  );
$obTxtCodRaca->setRotulo    ( "Raça/Cor"                   );
$obTxtCodRaca->setTitle     ( "Informe a Raça do Servidor." );
$obTxtCodRaca->setValue     ( $inCodRaca                   );
$obTxtCodRaca->setNull      ( false                        );
$obTxtCodRaca->setMaxLength ( 1                            );
$obTxtCodRaca->setInteiro   ( true                         );
$obTxtCodRaca->setSize      ( 10                           );

$obCmbRaca = new Select;
$obCmbRaca->setName       ( "stRaca"                     );
$obCmbRaca->setRotulo     ( "Raça/cor"                   );
$obCmbRaca->setTitle      ( "Informe a Raça do Servidor." );
$obCmbRaca->setValue      ( $inCodRaca                   );
$obCmbRaca->setNull       ( false                        );
$obCmbRaca->setStyle      ( "width: 250px"               );
$obCmbRaca->addOption     ( "", "Selecione"              );
$obCmbRaca->setCampoID    ( "[cod_rais]"                 );
$obCmbRaca->setCampoDesc  ( "[nom_raca]"                 );
$obCmbRaca->preencheCombo ( $rsRaca                      );

$obBscCID = new BuscaInner;
$obBscCID->setRotulo                       ( "CID"                                      );
$obBscCID->setTitle                        ( "Informe o CID do servidor, caso exista."  );
$obBscCID->setNull                         ( true                                       );
$obBscCID->setName                         ( "stCID"                                    );
$obBscCID->setId                           ( "stCID"                                    );
$obBscCID->setValue                        ( $stDescricaoCID                            );
$obBscCID->obCampoCod->setName             ( "inSiglaCID"                               );
$obBscCID->obCampoCod->setId               ( "inSiglaCID"                               );
$obBscCID->obCampoCod->setValue            ( $inSiglaCID                                );
$obBscCID->obCampoCod->setInteiro          ( false                                      );
$obBscCID->obCampoCod->setSize             ( 10                                         );
$obBscCID->obCampoCod->setAlign            ( "left"                                     );
$obBscCID->obCampoCod->setToUpperCase      ( true                                       );
$obBscCID->obCampoCod->obEvento->setOnBlur ( "buscaValor('buscaCIDServidor',1);"        );
$obBscCID->setFuncaoBusca                  ( "abrePopUp('".CAM_GRH_PES_POPUPS."CID/FLProcurarCID.php','frm','inSiglaCID','stCID','','".Sessao::getId()."','800','550')" );

$obHdnCodCID = new Hidden;
$obHdnCodCID->setName        ( "inCodCID"                                               );
$obHdnCodCID->setId          ( "inCodCID"                                               );
$obHdnCodCID->setValue       ( $inCodCID                                                );

$obDtDataLaudo  = new Data;
$obDtDataLaudo->setName      ( "dtDataLaudo" );
$obDtDataLaudo->setValue     ( $dtDataLaudo );
$obDtDataLaudo->setRotulo    ("Data do Laudo ");
$obDtDataLaudo->setTitle     ("Informe a data do laudo");
$obDtDataLaudo->setNull      ( true );

//Nacionalidade
$obTxtNacionalidade = new Label;
$obTxtNacionalidade->setRotulo ( "Nacionalidade"   );
$obTxtNacionalidade->setValue  ( $stNacionalidade  );
$obTxtNacionalidade->setId     ( "stNacionalidade" );

// Estado Naturalidade
$obTxtCodUFOrigem = new TextBox;
$obTxtCodUFOrigem->setName               ( "inCodUF"                               );
$obTxtCodUFOrigem->setRotulo             ( "UF Naturalidade"                       );
$obTxtCodUFOrigem->setValue              ( $inCodUF                                );
$obTxtCodUFOrigem->setTitle              ( "Selecione o estado da naturalidade."    );
$obTxtCodUFOrigem->setSize               ( 10                                      );
$obTxtCodUFOrigem->setMaxLength          ( 2                                       );
$obTxtCodUFOrigem->setInteiro            ( true                                    );
$obTxtCodUFOrigem->setNull               ( false                                   );
$obTxtCodUFOrigem->obEvento->setOnChange ( "buscaValor('preencheMunicipioOrigem',1);" );

$obRPessoalServidor->recuperaTodosUF( $rsUF );
$obCmbCodUFOrigem = new Select;
$obCmbCodUFOrigem->setName                  ( "stNomeUF"                              );
$obCmbCodUFOrigem->setRotulo                ( "UF Naturalidade"                       );
$obCmbCodUFOrigem->setValue                 ( $inCodUF                                );
$obCmbCodUFOrigem->setTitle                 ( "Selecione o estado da naturalidade."    );
$obCmbCodUFOrigem->setNull                  ( false                                   );
$obCmbCodUFOrigem->setCampoId               ( "[cod_uf]"                              );
$obCmbCodUFOrigem->setCampoDesc             ( "[nom_uf]"                              );
$obCmbCodUFOrigem->addOption                ( "", "Selecione"                         );
$obCmbCodUFOrigem->preencheCombo            ( $rsUF                                   );
$obCmbCodUFOrigem->setStyle                 ( "width: 250px"                          );
$obCmbCodUFOrigem->obEvento->setOnChange    ( "buscaValor('preencheMunicipioOrigem',1);" );

//MUNICIPIO NATURALIDADE
//$obRPessoalServidor->recuperaTodosMunicipio( $rsMunicipio );
$obTxtCodMunicipioOrigem = new TextBox;
$obTxtCodMunicipioOrigem->setRotulo      ( "Município Naturalidade"     );
$obTxtCodMunicipioOrigem->setName        ( "inCodMunicipio" );
$obTxtCodMunicipioOrigem->setValue       ( $inCodMunicipio  );
$obTxtCodMunicipioOrigem->setTitle       ( "Selecione a naturalidade do servidor." );
$obTxtCodMunicipioOrigem->setSize        ( 10    );
$obTxtCodMunicipioOrigem->setMaxLength   ( 4     );
$obTxtCodMunicipioOrigem->setNull        ( false );
$obTxtCodMunicipioOrigem->setInteiro     ( true  );

$obCmbCodMunicipioOrigem = new Select;
$obCmbCodMunicipioOrigem->setName           ( "stNomeMunicipio"                      );
$obCmbCodMunicipioOrigem->setRotulo         ( "Município Naturalidade"               );
$obCmbCodMunicipioOrigem->setValue          ( $inCodMunicipio                        );
$obCmbCodMunicipioOrigem->setTitle          ( "Selecione a naturalidade do servidor." );
$obCmbCodMunicipioOrigem->setNull           ( false                                  );
$obCmbCodMunicipioOrigem->setCampoId        ( "cod_municipio"                        );
$obCmbCodMunicipioOrigem->setCampoDesc      ( "nom_municipio"                        );
$obCmbCodMunicipioOrigem->addOption         ( "", "Selecione"                        );
$obCmbCodMunicipioOrigem->setStyle          ( "width: 250px"                         );

//Endereço
$obLblEndereco = new Label;
$obLblEndereco->setRotulo        ( "Endereço"                );
$obLblEndereco->setId            ( "stEnderecoIdentificacao" );
$obLblEndereco->setValue         ( $stEnderecoIdentificacao  );

//Bairro
$obLblBairro = new Label;
$obLblBairro->setRotulo        ( "Bairro"                );
$obLblBairro->setId            ( "stBairroIdentificacao" );
$obLblBairro->setValue         ( $stBairroIdentificacao  );

//UF
$obLblUF = new Label;
$obLblUF->setRotulo        ( "UF"                );
$obLblUF->setId            ( "stUFIdentificacao" );
$obLblUF->setValue         ( $stUFIdentificacao  );

//Município
$obLblMunicipio = new Label;
$obLblMunicipio->setRotulo        ( "Município"  );
$obLblMunicipio->setId            ( "stMunicipioIdentificacao" );
$obLblMunicipio->setValue         ( $stMunicipioIdentificacao  );

//CEP
$obLblCEP = new Label;
$obLblCEP->setRotulo        ( "CEP"  );
$obLblCEP->setId            ( "stCEPIdentificacao" );
$obLblCEP->setValue         ( $stCEPIdentificacao  );

//Telefone
$obLblTelefone = new Label;
$obLblTelefone->setRotulo        ( "Fone"  );
$obLblTelefone->setId            ( "stTelefoneIdentificacao" );
$obLblTelefone->setValue         ( $stTelefoneIdentificacao  );

//Escolaridade
$obLblEscolaridade = new Label;
$obLblEscolaridade->setRotulo        ( "Escolaridade"  );
$obLblEscolaridade->setId            ( "stEscolaridade" );
$obLblEscolaridade->setValue         ( $stEscolaridade  );

//Cod Servidor
$obHdnCodServidor = new Hidden;
$obHdnCodServidor->setName          ( "inCodServidor" );
$obHdnCodServidor->setValue         ( $inCodServidor  );

//Nome Fotor
$obHdnNomeFoto = new Hidden;
$obHdnNomeFoto->setName          ( "stNomeFoto" );
$obHdnNomeFoto->setValue         ( $stNomeFoto  );
