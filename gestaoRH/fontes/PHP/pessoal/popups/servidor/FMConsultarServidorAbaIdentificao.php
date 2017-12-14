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

$Revision: 30547 $
$Name$
$Author: souzadl $
$Date: 2006-10-25 07:42:42 -0300 (Qua, 25 Out 2006) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

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

$obFormularioFoto = new Formulario;
$obFormularioFoto->addComponente( $obImgFoto );
$obFormularioFoto->montaInnerHTML();

$obSpnFoto = new Span;
$obSpnFoto->setId    ( "spnFoto"                   );
$obSpnFoto->setValue ( $obFormularioFoto->getHTML() );

$obLblCGM = new Label;
$obLblCGM->setRotulo( "CGM"      );
$obLblCGM->setValue ( $inNumCGM."-".$stNomCGM  );
$obLblCGM->setId    ( "inNomCGM" );

$obLblDataNascimento = new Label;
$obLblDataNascimento->setRotulo ( "Data de Nascimento" );
$obLblDataNascimento->setValue  ( $stDataNascimento    );
$obLblDataNascimento->setId     ( "stDataNascimento"   );

$obLblSexo = new Label;
$obLblSexo->setRotulo ( "Sexo"   );
$obLblSexo->setValue  ( $stSexo  );
$obLblSexo->setId     ( "stSexo" );

$obLblPai = new Label;
$obLblPai->setRotulo           ( "Nome do Pai"                        );
$obLblPai->setValue            ( $stNomePai                           );
$obLblPai->setName             ( "stNomePai"                          );

$obLblMae = new Label;
$obLblMae->setRotulo           ( "Nome da Mãe"                        );
$obLblMae->setValue            ( $stNomeMae                           );
$obLblMae->setName             ( "stNomeMae"                          );

include_once(CAM_GA_CGM_MAPEAMENTO."TEstadoCivil.class.php");
$obTEstadoCivil = new TEstadoCivil;
$stFiltro = " WHERE cod_estado = ".$inCodEstadoCivil;
$obTEstadoCivil->recuperaTodos( $rsEstadoCivil, $stFiltro, "nom_estado");
$obLblCodEstadoCivil = new Label;
$obLblCodEstadoCivil->setName      ( "inCodEstadoCivil"                     );
$obLblCodEstadoCivil->setRotulo    ( "Estado Civil"                         );
$obLblCodEstadoCivil->setValue     ( $inCodEstadoCivil."-".$rsEstadoCivil->getCampo("nom_estado")                      );

$obLblCGMConjuge = new Label();
$obLblCGMConjuge->setRotulo               ( 'CGM do Cônjuge'       );
$obLblCGMConjuge->setValue                ( $inCGMConjuge."-".$stNomConjuge );
$obLblCGMConjuge->setName                 ( 'inCGMConjuge' );

include_once(CAM_GA_CGM_MAPEAMENTO."TRaca.class.php");
$obTRaca = new TRaca;
$stFiltro = " WHERE cod_rais = ".$inCodRaca;
$obTRaca->recuperaTodos( $rsRaca, $stFiltro, "nom_raca");
$obLblCodRaca = new Label;
$obLblCodRaca->setName      ( "inCodRaca"                  );
$obLblCodRaca->setRotulo    ( "Raça/Cor"                   );
$obLblCodRaca->setValue     ( $inCodRaca ."-".$rsRaca->getCampo("nom_raca")                  );

$rsCID = new recordset();
if (trim($inCodCID) != "") {
    $obRPessoalServidor->obRPessoalCID->listarOrdenadoPorDescricao( $rsCID );
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php");
    $obTPessoalCID = new TPessoalCID();
    $stFiltro = " WHERE cod_cid = ".$inCodCID;
    $obTPessoalCID->recuperaTodos( $rsCID , $stFiltro);
}
$obLblCodCID = new Label;
$obLblCodCID->setRotulo    ( "CID"                                    );
$obLblCodCID->setName      ( "inCodCID"                               );
$obLblCodCID->setValue     ( $inCodCID ."-".$rsCID->getCampo("descricao")                               );

//Nacionalidade
$obLblNacionalidade = new Label;
$obLblNacionalidade->setRotulo ( "Nacionalidade"   );
$obLblNacionalidade->setValue  ( $stNacionalidade  );
$obLblNacionalidade->setId     ( "stNacionalidade" );

// Estado Naturalidade
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
$obTUF = new TUF;
$stFiltro = " WHERE cod_uf = ".$inCodUF;
$obTUF->recuperaTodos( $rsUF, $stFiltro);
$obLblCodUFOrigem = new Label;
$obLblCodUFOrigem->setName               ( "inCodUF"                               );
$obLblCodUFOrigem->setRotulo             ( "UF Naturalidade"                       );
$obLblCodUFOrigem->setValue              ( $inCodUF."-".$rsUF->getCampo("nom_uf")                               );

//MUNICIPIO NATURALIDADE
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
$obTMunicipio = new TMunicipio;
$stFiltro = " WHERE cod_uf = ".$inCodUF;
$stFiltro .= " AND cod_municipio = ".$inCodMunicipio;
$obTMunicipio->recuperaTodos( $rsMunicipioOrigem, $stFiltro );
$obLblCodMunicipioOrigem = new Label;
$obLblCodMunicipioOrigem->setRotulo      ( "Município Naturalidade"     );
$obLblCodMunicipioOrigem->setName        ( "inCodMunicipio" );
$obLblCodMunicipioOrigem->setValue       ( $inCodMunicipio ."-".$rsMunicipioOrigem->getCampo("nom_municipio") );

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
