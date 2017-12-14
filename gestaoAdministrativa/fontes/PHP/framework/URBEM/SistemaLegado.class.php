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
 *
 * Data de Criação: 27/10/2005

 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Cassiano de Vasconcellos Ferreira

 * @package framework
 * @subpackage componentes

 Casos de uso: uc-01.01.00

 $Id: SistemaLegado.class.php 65087 2016-04-22 14:27:07Z carlos.silva $

 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
class SistemaLegado
{
public static function pegaConfiguracao($stParametro,$inCodModulo=2,$inExercicio="", $boTransacao = "")
{
    $stSQL =  "select cod_modulo, parametro, valor
                from administracao.configuracao
            where cod_modulo =".$inCodModulo." and parametro='".$stParametro."'";
    if ($inExercicio != "") {
        $stSQL .= " and exercicio = '".$inExercicio."'";
    } else {
        $stSQL .= " and exercicio <= '".Sessao::getExercicio() . "'";
        $stSQL .= " order by exercicio desc limit 1 ";
    }
    $obConexao = new Conexao;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsRecordSet->eof() ) {
            $stValor = $rsRecordSet->getCampo( 'valor' );
        } else {
            $stValor = $stParametro." não encontrado para o módulo ".$inCodModulo;
        }
    } else {
        $stValor = $obErro->getDescricao();
    }

    return $stValor;
}

// Função criada em virtude da alteração para o novo plano de contas e adaptação para o TRIBUNA DE CONTAS DO ESTADO DE MATO GROSSO DO SUL
// Retora true caso o sistema estiver sendo executado neste tribunal
// Usa o CNPJ do tribunal para verificar
public static function is_tcems($boTransacao='')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cnpj'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('15424948000141') ) && ( Sessao::getExercicio() > '2011' ) ) {
        return true;
    }

    return false;
}

// Função criada em virtude da alteração da emissão da nota avulsa juntamente com o carnê
// Retora true caso o sistema estiver sendo executado em manaquiri
// Usa o CNPJ de manaquiri para verificar
public static function is_manaquiri($boTransacao='')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cnpj'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('04641551000195') ) ) {
        return true;
    }

    return false;
}

public static function isRS($boTransacao = '')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cod_uf'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('23') ) ) {
        return true;
    }

    return false;
}

public static function isAL($boTransacao = '')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cod_uf'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('2') ) ) {
        return true;
    }

    return false;
}

public static function isTCMGO($boTransacao = '')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cod_uf'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('9') ) ) {
        return true;
    }

    return false;
}

public static function isTCEMG($boTransacao = '')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cod_uf'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";

    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('11') ) ) {
        return true;
    }

    return false;
}

public static function isTCMBA($boTransacao = '')
{
    $stSql = "SELECT
                valor
              FROM
                administracao.configuracao
              WHERE
                    cod_modulo = 2
                AND parametro  = 'cod_uf'
                AND exercicio  = '".Sessao::getExercicio()."'
            ";
    
    $obConexao   = new Conexao;
    $obErro      = new Erro;
    $obRecordSet = new RecordSet;
    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return false;
    }
    if ( ( trim($rsRecordSet->getCampo('valor')) == trim('5') ) ) {
        return true;
    }

    return false;
}

/**************************************************************************/
/**** Retorna um dado de qualquer tabela                                ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 18:21:34 BRT 2003
public static function pegaDado($sDado,$sTabela,$sWhere, $boTransacao = "")
{
    $stValor = "";
    $stSQL = "select $sDado from $sTabela $sWhere";
    $obConexao = new Conexao;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao, $obConexao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsRecordSet->eof() ) {
            $stValor = $rsRecordSet->getCampo( $sDado );
        }
    } else {
        $stValor = $obErro->getDescricao();
    }

    return $stValor;
}

public static function executaFramePrincipal($stJs)
{
    print '<script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.frames["telaPrincipal"].document.frm;
                var d = window.parent.frames["telaPrincipal"].document;
                var aux;
                '.$stJs.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           </script>';
}

public static function executaFrameOculto($stJs)
{
    $stScripts = "";
    if ( substr( basename( $_SERVER['PHP_SELF'] ), 0, 2 ) == "OC"  ) {
        $stScripts .= '<script src="'.CAM_GA.'javaScript/ifuncoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/funcoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/genericas.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/Window.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/mascaras.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/tipo.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/arvore.js" type="text/javascript"></script>
';
    }

    print  $stScripts.'
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.frames["telaPrincipal"].document.frm;
                var d = window.parent.frames["telaPrincipal"].document;
                var jq_ = window.parent.frames["telaPrincipal"].jQuery;
                var aux;
                '.$stJs.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           executa();
           </script>';
}

public static function executaiFrameOculto($stJs)
{
    print '<html>
           <head>
           <script src="'.CAM_GA.'javaScript/ifuncoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/funcoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/genericas.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/Window.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/mascaras.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/tipo.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/arvore.js" type="text/javascript"></script>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.document.frm;
                var d = window.parent.document;
                var jq_ = window.parent.document.jQuery;
                var aux;
                '.$stJs.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           </script>
           </head>
           <body onLoad="javascript:executa();">
           </body>
           </html>';
}

public static function executaWindowOpener($stJs)
{
    print '<html>
           <head>
           <script src="'.CAM_GA.'javaScript/ifuncoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/funcoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/genericas.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/Window.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/mascaras.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/tipo.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/arvore.js" type="text/javascript"></script>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.opener.document.frm;
                var d = window.parent.opener.document;
                var jq_ = window.parent.opener.jQuery;
                var aux;
                '.$stJs.'

                if (erro) {
                    alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
                } else {
                    window.parent.close();
                }
           }
           </script>
           </head>
           <body onLoad="javascript:executa();">
           </body>
           </html>';
}

public static function unhtmlentities($stString)
{
    $stString = str_replace('&nbsp;',' ',$stString);
    $stString = str_replace('&gt;','>',$stString);
    $stString = str_replace('&lt;','<',$stString);

    return $stString;
}

public static function exibeAviso($objeto="",$tipo="n_incluir",$chamada="erro")
{
    print '<script type="text/javascript">
                alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'");
           </script>';
}

public static function exibeAvisoTelaPrincipal($objeto="",$tipo="n_incluir",$chamada="erro")
{
    print '<script type="text/javascript">
                alertaAvisoTelaPrincipal("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'");
           </script>';
}

/**************************************************************************/
/**** Chama o script com mensagem de alerta e redireciona               ***/
/**** Autor: Ricardo Lopes Seg Abr  7 15:08:06 UTC 2003                 ***/
/**************************************************************************/
/* Exemplo:
    alertaAviso("incluiNovo.php","Usuário","incluir","aviso"); */
public static function alertaAviso($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao="", $caminho="")
{
    //print "<br> id: ".Sessao::getId()."<br>";
    //echo $caminho."<br>";
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".(array_key_exists(1, $aux) ? $aux[1] : '');
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                mudaTelaPrincipal("'.$location.'");
           </script>';
}
public static function mudaFrameOculto($location)
{
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                mudaFrameOculto("'.$location.'");
           </script>';
}

public static function mudaFramePrincipal($location)
{
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                mudaTelaPrincipal("'.$location.'");
           </script>';
}

/*
Função para exibir mensagem de finalização de processamento em um popup.
Ao terminar o processamento essa função envia uma mensagem para a tela principal
e depois fecha automaticamente o popup.
*/
public static function alertaAvisoPopUpPrincipal($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao, $caminho="")
{
    //print "<br> id: ".Sessao::getId()."<br>";
    //echo $caminho."<br>";
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAvisoPopUpPrincipal("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                window.close();
                window.opener.focus();
           </script>';
}

public static function alertaAvisoPopUp($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao = "", $caminho="")
{
    //print "<br> id: ".Sessao::getId()."<br>";
    //echo $caminho."<br>";
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona para o proprio PopUp
    print '<script type="text/javascript">
                <!--alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");-->
                parent.window.location.replace("'.$location.'");
           </script>';
}

/* Funções para bloquear frames     */
/* Lucas Stephanou || 01/03/2005    */
public static function BloqueiaFrames($boPrincipal=true,$boMenu=false)
{
    $ini = "<script type=\"text/javascript\">\r\n";
    $fim =" \r\n</script>\r\n";
    if ($boMenu ==true) {echo $ini."BloqueiaFrames(true,true);".$fim   ;}
    if ($boPrincipal==true) {echo $ini."BloqueiaFrames(true,false);".$fim   ;}
    ob_flush();
}
public static function LiberaFrames($boPrincipal=true,$boMenu=true)
{
    $ini = "<script type=\"text/javascript\">\r\n";
    $fim = "\r\n</script>\r\n";
    if ($boMenu ==true) {echo $ini."LiberaFrames(true,true);".$fim   ;}
    if ($boPrincipal==true) {echo $ini."LiberaFrames(true,false);".$fim   ;}

}
public static function comparaDatas($stData1,$stData2,$maiorIgual = false)
{
    list( $dia1,$mes1,$ano1 ) = explode( '/', $stData1 );
    list( $dia2,$mes2,$ano2 ) = explode( '/', $stData2 );

    if ($maiorIgual) {
        if ("$ano1$mes1$dia1" >= "$ano2$mes2$dia2" )return true;
        else return false;
    } else {
        if ("$ano1$mes1$dia1" > "$ano2$mes2$dia2" )return true;
        else return false;
    }
}

//Autor: Jorge Ribarr Sex Fev 28 10:54:16 BRT 2003
public static function mostraVar($vVariavel)
{
    print "<div align='left'><pre class=\"debug\">\n";
    echo '<h7 class="debug">MostraVar</h7>';
    print_r($vVariavel);
    print "\n</pre></div>";
}

public static function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);
}

/**************************************************************************/
/**** Retorna um valor por extenso em Reais                             ***/
/**** Autor: Leonardo Tremper                                           ***/
/**************************************************************************/
/*  Esta função recebe um valor numérico e retorna uma string contendo o
    valor de entrada por extenso.
    entrada: $valor (use ponto para centavos.)
    Ex.:

    echo extenso("12428.12"); //retorna: doze mil, quatrocentos e vinte e oito reais e doze centavos

    ou use:
    echo extenso("12428.12", true); //esta linha retorna: Doze Mil, Quatrocentos E Vinte E Oito Reais E Doze Centavos

    saída..: string com $valor por extenso em reais e pode ser com iniciais em maiúsculas (true) ou não.

*/
public static function extenso($valor=0, $maiusculas=false)
{
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

    $z=0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for($i=0;$i<count($inteiro);$i++)
        for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
            $inteiro[$i] = "0".$inteiro[$i];

    $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
    for ($i=0;$i<count($inteiro);$i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
        $t = count($inteiro)-1-$i;
        $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")$z++; elseif ($z > 0) $z--;
        if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
        if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

         if (!$maiusculas) {
                          return($rt ? $rt : "zero");
         } else {
                          return (ucwords($rt) ? ucwords($rt) : "Zero");
         }

}

/**************************************************************************/
/**** Retorna por extenso                                               ***/
/**************************************************************************/
//Autor: Jorge Ribarr Qui Mar 13 14:13:08 BRT 2003
//print dataExtenso('2003-03-24',true);  Segunda-feira, 24 de Março de 2003.
//print dataExtenso('2003-03-24');       24 de Março de 2003.
public static function dataExtenso($sData,$bComDiaSemana=false)
{
    if (strlen($sData)==10) {
        $aDiaSem = array("Domingo", "Segunda-feira", "Terça-Feira",
                        "Quarta-Feira", "Quinta-Feira", "Sexta-Feira",
                        "Sábado");
        $aMes = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio",
                        "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro",
                        "Dezembro");
        $aData   = explode('-',$sData);
        $dData   = mktime (0,0,0,(int) $aData[1],(int) $aData[2],(int) $aData[0]);
        $iDiaSem = (int) date('w',$dData);
        $iMes    = (int) date('m',$dData) - 1;
        $sAno    = date('Y',$dData);
        $sDia    = date('d',$dData);
        $sDiaSem = $aDiaSem[$iDiaSem];
        $sMes    = $aMes[$iMes];
        if ($bComDiaSemana) {
            $sAux = "$sDiaSem, $sDia de $sMes de $sAno.";
        } else {
            $sAux = "$sDia de $sMes de $sAno.";
        }
    } else {
        $sAux = "$sData, é uma data inválida.";
    }

    return $sAux;
}

/**************************************************************************/
/**** Data formato SQL para formato Brasileiro                         ***/
/**************************************************************************/
//dataToBr('2003-02-24'); retorna 24/02/2003
public static function dataToBr($data)
{
    if ($data != '') {
        $data = substr($data,0,10);
        $data = str_replace("/","-","$data");
        $data = str_replace(".","-","$data");
        $ArrData = explode("-", $data);
        $data = $ArrData[2] . "/" . $ArrData[1] . "/" . $ArrData[0];
    } else {
        $data = '';
    }

    return $data;
}

/**************************************************************************/
/**** Data formato Brasileiro para formato  SQL                        ***/
/**************************************************************************/
//dataToSql('01/01/2011'); retorna 2011-01-01
public static function dataToSql($data)
{
$arrData = explode('/', $data);
$data = $arrData[2] . "-" . $arrData[1] . "-" . $arrData[0];
return $data;
}

/**************************************************************************/
//** Função que valida um valor através de uma máscara dinâmica
/**************************************************************************/
//Criado Por: Cassiano Ferreira e Ricardo Lopes
//Ex.: validaMascara("999.99-9999/9","1.22-564")
//retorna 001.22-0564/0
public static function validaMascaraDinamica($mascara,$digitos)
{
    $mascaraFinal = "";
    $erros = 0;

    //Explode a mascara
    $elementosMascara =  preg_split( "/[^a-zA-Z0-9]/",$mascara);

    //Pega somente os separadores da máscara
    $separadoresMascara =  preg_replace( "/[a-zA-Z0-9]/","",$mascara);

    //Total de elementos  da mascara
    $totalElMascara = sizeof($elementosMascara);

    //explode o digito
    $digitosMascara = preg_split( "/[^a-zA-Z0-9]/",$digitos);

    //Total de elementos  do digito
    $totalDigitosMasc = sizeof($digitosMascara);

    //Inicia laço para comparação
    for ($i = 0; $i <= $totalElMascara; $i++) {
        //qtd algarismos na mascara e digitos
        $chMasc = (int) strlen($elementosMascara[$i]);
        $chDigi = (int) strlen($digitosMascara[$i]);

        if ($chDigi > $chMasc) {
            $erros++;
        }

        $chDiff = $chMasc - $chDigi;
        $chZeros = "";

        //laço para inserção de zeros
        for ($e = 0; $e < $chDiff; $e++) {
            $chZeros .= "0";
        }

        $separador = substr($separadoresMascara,$i,1);
        $mascaraFinal .= $chZeros.$digitosMascara[$i].$separador;
    }

    $tot = strlen($mascaraFinal);
    //$tot = $tot-2;
    //$mascaraFinal = substr($mascaraFinal, 0, $tot);

    if ($erros == 0) {
        $aMascara[0] = 1;
        $aMascara[1] = $mascaraFinal;
    } else {
        $aMascara[0] = 0;
        $aMascara[1] = "";
    }

    return $aMascara;
}// Fim da função validaMascaraDinamica

public static function debugRequest()
{
    echo "<div align='left'>\n<b>POST</b><br>\n";
    foreach ($_POST as $key=>$value) {
        echo "$key = $value<br>\n";
    }
    echo "<b>GET</b><br>\n";
    foreach ($_GET as $key=>$value) {
        echo "$key = $value<br>\n";
    }
    echo "</div>";
}

/**
* Retorna ultimo dia util do mes
*
* @author: Lucas Stephanou
* @params: $hoje = data US para qual ele pegara o mes e retorna o ultimo dia
*/
public static function ultimaDiaUtilMes($hoje = '')
{
    if ( $hoje == '')
        $hoje= date('Y-m-d');
    $arData = explode('-',$hoje);
    $ano = $arData[0];
    $mes = $arData[1];
    $dia = 01; // dia sempre é primeiro

    // array de ultimos dias do mes
    $arMeses =array("01" => "31", "02" => "28","03" => "31","04" => "30","05" => "31","06"=>"30","07"=>"30","08" => "31","09" => "31","10" => "31","11" => "30","12" => "31");

    //monta nova data
    $dia=$arMeses[$mes];
    $dtData = "$ano-$mes-$dia";

    // checar mes de fev
    if ($mes == 2) {
        $arTmp = explode('-',$dtData);
        if (!checkdate($arTmp[1],$arTmp[2],$arTmp[0])) {
            $dia = '29';
            $dtData = "$ano-$mes-$dia";
        } else {
            $dia = $arMeses[$mes];
            $dtData = "$ano-$mes-".$arMeses[$mes];
        }
    }
    //strtotime data para timestamp
    $diaSemana = date('w',strtotime($dtData));

    switch ($diaSemana) {
        case 0:
            $dia -= 2;
            break;
        case 6:
            $dia -= 1;
            break;
    }

    // formata dia/mes
    $dia = str_pad($dia,2,'0',STR_PAD_LEFT);
    $mes = str_pad($mes,2,'0',STR_PAD_LEFT);

    //monta data novamente
    $data = "$ano-$mes-$dia";

    return "$data";
}

public static function exibirAjuda($stCaminho)
{
if ( is_file(  $stCaminho ) ) {
    $stHTML = <<<HEREDOC
            <!-- INICIO AJUDA -->
            <a id="link_ajuda" href="" onclick="if (winList['sample1']) winList['sample1'].open(); return false;" alt="Ajuda">

            <span alt="Ajuda">Ajuda</span>
            </a>
            <div id="sample1" class="window" style="left:-3000px;top:30px;width:600px;">
                <div class="titleBar">
                    <span class="titleBarText">Ajuda</span>
                    <img class="titleBarButtons" alt="" src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/buttons.gif" usemap="#sampleMap1" />
                    <map id="sampleMap1" name="sampleMap1">
                        <area shape="rect" coords="0,0,15,13"  href="" alt="" title="Minimizar" onclick="this.parentWindow.minimize();return false;" />

                        <area shape="rect" coords="16,0,31,13" href="" alt="" title="Restaurar"  onclick="this.parentWindow.restore();return false;" />
                        <area shape="rect" coords="34,0,49,13" href="" alt="" title="Fechar"    onclick="this.parentWindow.close();return false;" />
                    </map>
                </div>
                <div class="clientArea" style="height:200px;">
                <iframe src="$stCaminho" height="98%" width="99%"></iframe>
                </div>
            </div>
            <!-- FIM AJUDA-->
HEREDOC;
echo $stHTML;
}
}

/**
* Retorna a diferença em anos entre duas datas
*
* @author: Diego Lemos de Souza
* @params: $dtInicial = 00/00/0000
*          $dtFinal   = 00/00/0000 default data atual
*/
public static function diferencaEntreDatas($dtInicial,$dtFinal="")
{
    $dtFinal = ( $dtFinal != "" ) ? $dtFinal : date("d/m/Y");
    list($inDia,$inMes,$inAno)    = explode("/",$dtInicial);
    list($inDiaF,$inMesF,$inAnoF) = explode("/",$dtFinal);
    if ((int) $inMesF > $inMes) {
        $inDiferenca = $inAnoF - $inAno;
    } elseif ((int) $inMesF == $inMes) {
        if ((int) $inDiaF >= $inDia) {
            $inDiferenca = $inAnoF - $inAno;
        } elseif ((int) $inDiaF < $inDia) {
            $inDiferenca = $inAnoF - $inAno - 1;
        }
    } elseif ((int) $inMesF < $inMes) {
        $inDiferenca = $inAnoF - $inAno - 1;
    }

    return $inDiferenca;
}

public static function diferencaExataEntreDatas($dtInicial,$dtFinal="")
{
    $dtFinal = ( $dtFinal != "" ) ? $dtFinal : date("d/m/Y");
    $data1 = explode("/", $dtInicial);
    $data2 = explode("/", $dtFinal);

    $ano = $data2[2] - $data1[2];
    $mes = $data2[1] - $data1[1];
    $dia = $data2[0] - $data1[0];
    if ($mes < 0) {
        $ano--;
        $mes = 12 + $mes;
    }
    if ($dia < 0) {
        $mes--;
        $dia = 30 + $dia;
    }

    if($ano > 0) $arDiff[]  = $ano;
    //if($ano > 1) $str_ano .= 's';

    if($mes > 0) $arDiff[]  = $mes;
    //if ($mes > 1) {if($ano > 0)$str_ano .= ' e '; $str_mes .= 'es'; }

    if($dia > 0) $arDiff[]  = $dia;
    //if ($dia > 1) {if($mes > 0)$str_mes .= ' e '; $str_dia .= 's'; }
    return $arDiff;
}

/**************************************************************************/
/**** Converte um número inteiro para decimal                           ***/
/**** Se numero 1234 , casas 3 retorna 1.234                            ***/
/**** Autor: Vandré Miguel Ramos  - 18/10/2006                          ***/
/**************************************************************************/
public static function intToDecimal($valor,$casas)
{
    if ($casas > 0) {
       $casas = $casas * -1;
    }
    $valor = $valor * pow(10,$casas);

    return $valor;
}

 public static function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
 {
 /*
 $interval can be:
 yyyy - Number of full years
 q - Number of full quarters
 m - Number of full months
 y - Difference between day numbers
 (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
 d - Number of full days
 w - Number of full weekdays
 ww - Number of full weeks
 h - Number of full hours
 n - Number of full minutes
 s - Number of full seconds (default)
 */

     if (!$using_timestamps) {
         $datefrom = strtotime($datefrom, 0);
         $dateto = strtotime($dateto, 0);
     }
     $difference = $dateto - $datefrom; // Difference in seconds

     switch ($interval) {

         case 'yyyy': // Number of full years

             $years_difference = floor($difference / 31536000);
             if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                 $years_difference--;
             }
             if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                 $years_difference++;
             }
             $datediff = $years_difference;
             break;

         case "q": // Number of full quarters

             $quarters_difference = floor($difference / 8035200);
             while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                 $months_difference++;
             }
             $quarters_difference--;
             $datediff = $quarters_difference;
             break;

         case "m": // Number of full months

             $months_difference = floor($difference / 2678400);
             while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                 $months_difference++;
             }
             $months_difference--;
             $datediff = $months_difference;
             break;
         case "M": // Number of really months

             $months_difference = floor($difference / 2678400);
             while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                 $months_difference++;
             }
             $datediff = $months_difference;
             break;
         case 'y': // Difference between day numbers

             $datediff = date("z", $dateto) - date("z", $datefrom);
             break;

         case "d": // Number of full days

             $datediff = floor($difference / 86400);
             break;

         case "w": // Number of full weekdays

             $days_difference = floor($difference / 86400);
             $weeks_difference = floor($days_difference / 7); // Complete weeks
             $first_day = date("w", $datefrom);
             $days_remainder = floor($days_difference % 7);
             $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
             if ($odd_days > 7) { // Sunday
                 $days_remainder--;
             }
             if ($odd_days > 6) { // Saturday
                 $days_remainder--;
             }
             $datediff = ($weeks_difference * 5) + $days_remainder;
             break;

         case "ww": // Number of full weeks

             $datediff = floor($difference / 604800);
             break;

         case "h": // Number of full hours

             $datediff = floor($difference / 3600);
             break;

         case "n": // Number of full minutes

             $datediff = floor($difference / 60);
             break;

         default: // Number of full seconds (default)

             $datediff = $difference;
             break;
     }

     return $datediff;

 }

/*
Mascara o $input de acordo com a mascara informada em $mask utilizando o $joker como coringa
ex: $input = '1.2.3.4.5' $mask = '9.99.9.99' o retorno sera = '1.02.3.04'
 * Se nenhum valor for passado para mask então esta assume o valor da mascara da conta da contabilidade
**/
public static function doMask($input, $mask='', $joker='0', $boTransacao="")
{
    if ( isset($input) ) {

        if ($mask == '') {
            include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php");
            $obRContabilidadePlanoConta = new RContabilidadePlanoConta();
            $obRContabilidadePlanoConta->recuperaMascaraConta( $mask, $boTransacao );
        }

        $i = 0;
        $array_return = array();
        $array_valor = explode('.', $input);
        $array_mask  = explode('.', $mask);

        while ( $i < count($array_mask) ) {
            $array_return[$i] = str_pad( ( isset($array_valor[$i]) ? $array_valor[$i] : $joker ), strlen($array_mask[$i]), $joker, STR_PAD_LEFT);
            $i++;
        }

        return implode('.', $array_return);
    } else {
        return 'Params missing.';
    }
}

public static function mask($val, $mask)
{
    $maskared = '';
    $k = 0;
    for($i = 0; $i<=strlen($mask)-1; $i++)
    {
        if($mask[$i] == '#')
        {
            if(isset($val[$k]))
            $maskared .= $val[$k++];
        }
        else
        {
            if(isset($mask[$i]))
            $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

/* Converte todos os caracteres para maiúsculo, inclusive os caracteres especiais */
public static function strtoupper_ptBR($string)
{
    $string = strtoupper(str_replace(array("à","á","â","ã","ä","å","æ","ç","è","é","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","ô","õ","ö","ø","ù","ú","û","ü","ý"),
                                     array("À","Á","Â","Ã","Ä","Å","Æ","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ","Ö","Ø","Ù","Ú","Û","Ü","Ý"),
                                     $string));

    return $string;
}

/* Retorna último dia do mês em formato dd/mm/yyyy */
public static function retornaUltimoDiaMes($inMes, $exercicio)
{
    switch ($inMes) {
        case '01':
            $dt = '31/01/'.$exercicio;
        break;

        case '02':
            $dt = date('d/m/Y', strtotime("-1 days",strtotime('01-03-'.$exercicio)) );
        break;

        case '03':
            $dt = '31/03/'.$exercicio;
        break;

        case '04':
            $dt = '30/04/'.$exercicio;
        break;

        case '05':
            $dt = '31/05/'.$exercicio;
        break;

        case '06':
            $dt = '30/06/'.$exercicio;
        break;

        case '07':
            $dt = '31/07/'.$exercicio;
        break;

        case '08':
            $dt = '31/08/'.$exercicio;
        break;

        case '09':
            $dt = '30/09/'.$exercicio;
        break;

        case '10':
            $dt = '31/10/'.$exercicio;
        break;

        case '11':
            $dt = '30/11/'.$exercicio;
        break;

        case '12':
            $dt = '31/12/'.$exercicio;
        break;
    }

    return $dt;
}

/**
* Gera o novo código verificador sem utilização de Applet
*/
public static function gerarCodigoTerminal()
{
    if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {

        $stIP = $_SERVER['HTTP_X_FORWARDED_FOR'];

    } elseif ( isset($_SERVER['HTTP_CLIENT_IP']) ) {

        $stIP = $_SERVER['HTTP_CLIENT_IP'];

    } elseif ( isset($_SERVER['REMOTE_ADDR']) ) {

        $stIP = $_SERVER['REMOTE_ADDR'];

    }
    $stKey = str_replace('.','',$stIP);
    $stCodigo = md5($stKey);

    return $stCodigo;
}

public static function restringeCaracteres($text, $limit)
{
    $len = strlen($text);
    if ($len > $limit) {
        return substr($text, 0, $limit).'...';
    } else {
        return $text;
    }
}

/********************************************************************************/
/**** Soma ou Subtrai dia, mes ou ano a partir da data passada por parametro. ***/
/**** Se nao tiver data, data atual setada como padrao                        ***/
/**** Autor: Evandro Melos - 04/04/2013                                       ***/
/**** Formato da data : dd/mm/yyyy                                            ***/
/**** Formato do dia  : day                                                   ***/
/**** Formato do mes  : month                                                 ***/
/**** Formato do ano  : year                                                  ***/
/**** Formato da semana : week                                                ***/
/**** Formato da soma : true soma, false subtrai                              ***/
/**** Exemplo: somaOuSubtraiData('01/01/2013',true,2,'day')                   ***/
/********************************************************************************/
public static function somaOuSubtraiData($stData, $boSoma = true ,$inValor = 0, $stDiaOuMesOuAno = 'day')
{
    $stResultadoData = '';
    $stData = $stData ? str_replace("/","-",$stData) : date('d-m-Y');
    $stResultadoData = date('d/m/Y', strtotime( ($boSoma ? "+" : "-").$inValor." $stDiaOuMesOuAno ",strtotime($stData) ) );

    return $stResultadoData;
}

/********************************************************************************/
/**** Valida se o arquivo indicado existe.                                    ***/
/**** Se existir retorna True, senão False.                                   ***/
/**** Autor: Michel Teixeira - 09/01/2014                                     ***/
/********************************************************************************/
public static function validaArquivo($arquivo)
{
    $valida=false;
    if (file_exists($arquivo)==true) {
        $valida=true;
    }

    return $valida;
}

/***********************************************************/
/**** Ordena um array por uma ou mais indice            ****/
/**** $array : Array com os dados para ser ordenado     ****/
/**** $cols  : Array com os colunas que serão ordenadas ****/
/**** Ex: array("coluna"=>SORT_ASC,"coluna2"=>SORT_ASC) ****/
/**** Tipos de ordenação : SORT_ASC                     ****/
/****                    , SORT_DESC                    ****/
/****                    , SORT_REGULAR                 ****/
/****                    , SORT_NUMERIC                 ****/
/****                    , SORT_STRING                  ****/
/***********************************************************/
public static function ordenaArray($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $params = array();
    foreach ($cols as $col => $order) {
        $params[] =& $colarr[$col];
        $params = array_merge($params, (array)$order);
    }
    call_user_func_array('array_multisort', $params);
    $ret = array();
    $keys = array();
    $first = true;
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            if ($first) { $keys[$k] = substr($k,1); }
            $k = $keys[$k];
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
        $first = false;
    }
    return $ret;

}

/**************************************************************************************************************/
/**** Retorna o periodo Inicial e Final do Bimestra passado.                                               ****/
/**** $stDtInicial : variável que retorna por referência a data inicial do periodo. Formato : 'dd/mm/yyyy' ****/
/**** $stDtFinal   : variável que retorna por referência a data final do periodo. Formato : 'dd/mm/yyyy'   ****/
/**** $inBimestre  : variável que recebe o valor do bimestre solicitado                                    ****/
/**** $inExercicio : variável que recebe o valor do exercicio                                              ****/
/**** Exemplo de uso .: periodoInicialFinalBimestre($stDtInicial, $stDtFinal, 1, 2014 )                    ****/
/**************************************************************************************************************/
public static function periodoInicialFinalBimestre(&$stDtInicial, &$stDtFinal, $inBimestre, $inExercicio )
{
    switch($inBimestre) {
        case 1:
            $stDtInicial = '01/01/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(2,$inExercicio);
            break;
        case 2:
            $stDtInicial = '01/03/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(4,$inExercicio);
            break;
        case 3:
            $stDtInicial = '01/05/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(6,$inExercicio);
            break;
        case 4:
            $stDtInicial = '01/07/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(8,$inExercicio);
            break;
        case 5:
            $stDtInicial = '01/09/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(10,$inExercicio);
            break;
        case 6:
            $stDtInicial = '01/11/'.$inExercicio;
            $stDtFinal   = SistemaLegado::retornaUltimoDiaMes(12,$inExercicio);
            break;
    }
}


/**************************************************************************************************************/
/**** Retorna o mês em PT-BR tendo como parâmetro o mês em inteiro.                                        ****/
/**** $stDtInicial : variável que retorna por referência a data inicial do periodo. Formato : 'dd/mm/yyyy' ****/
/**** $stDtFinal   : variável que retorna por referência a data final do periodo. Formato : 'dd/mm/yyyy'   ****/
/**** $inBimestre  : variável que recebe o valor do bimestre solicitado                                    ****/
/**** $inExercicio : variável que recebe o valor do exercicio                                              ****/
/**** Exemplo de uso .: periodoInicialFinalBimestre($stDtInicial, $stDtFinal, 1, 2014 )                    ****/
/**************************************************************************************************************/
public static function mesExtensoBR($inMes)
{
    $arMes = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio",
                    "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro",
                    "Dezembro");
    
    return $arMes[$inMes-1];
}

/**************************************************************************
 Informa a query que será executada e o campo a ser retornado
 Exemplo:
   $codMinimo = pegaValor("Select min(cod) as minimo From Tabela","minimo");
/**************************************************************************/
//Autor: Ricardo Lopes Qua Abr  9 13:41:44 UTC 2003
public static function pegaValor($query,$campo)
{
    //Pega os dados encontrados em uma query
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($query);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
        if (!$conn->eof()) {
            $res = $conn->pegaCampo($campo);
        }
    $conn->limpaSelecao();

    return $res;
}

/*******************************************************************************************************************************/
/**** Retorna o periodo Inicial e Final dos Meses que estao no Periodo                                                      ****/
/**** $arDatas     : variável que retorna por referência a data inicial e final dos meses periodo. Formato : 'dd/mm/yyyy'   ****/
/**** $stTipoPeriodo : variável que recebe o valor do tipo de periodo                                                       ****/
/**** $inPeriodo  : variável que recebe o valor do periodo solicitado                                                       ****/
/**** $inExercicio : variável que recebe o valor do exercicio                                                               ****/
/**** Exemplo de uso .: retornaInicialFinalMesesPeriodicidade($arDatas, 'bimestre', 1, 2015 )                               ****/
/*******************************************************************************************************************************/
public static function retornaInicialFinalMesesPeriodicidade(&$arDatas,$stTipoPeriodo,$inPeriodo,$inExercicio)
{
    switch($stTipoPeriodo){
        case "bimestre":
        case "bimestral":
            $inMesInicial = ($inPeriodo*2)-1;
            for ($i=$inMesInicial; $i <= ($inPeriodo*2); $i++){ 
                $arDatas[] = array( 'stDtInicial' => "01/".str_pad($i,2,"0",STR_PAD_LEFT)."/".$inExercicio
                                    , 'stDtFinal' => SistemaLegado::retornaUltimoDiaMes($i,$inExercicio)
                                    );
            }
        break;

        case "trimestre":
        case "trimestral":
            $inMesInicial = ($inPeriodo*3)-2;
            for ($i=$inMesInicial; $i <= ($inPeriodo*3); $i++){ 
                $arDatas[] = array( 'stDtInicial' => "01/".str_pad($i,2,"0",STR_PAD_LEFT)."/".$inExercicio
                                    , 'stDtFinal' => SistemaLegado::retornaUltimoDiaMes($i,$inExercicio)
                                    );
            }
        break;

        case "quadrimestre":
        case "quadrimestral":
            $inMesInicial = ($inPeriodo*4)-3;
            for ($i=$inMesInicial; $i <= ($inPeriodo*4); $i++){ 
                $arDatas[] = array( 'stDtInicial' => "01/".str_pad($i,2,"0",STR_PAD_LEFT)."/".$inExercicio
                                    , 'stDtFinal' => SistemaLegado::retornaUltimoDiaMes($i,$inExercicio)
                                    );
            }
        break;

        case "semestre":
        case "semestral":
            $inMesInicial = ($inPeriodo*6)-6;
            for ($i=$inMesInicial; $i <= ($inPeriodo*6); $i++){ 
                $arDatas[] = array( 'stDtInicial' => "01/".str_pad($i,2,"0",STR_PAD_LEFT)."/".$inExercicio
                                    , 'stDtFinal' => SistemaLegado::retornaUltimoDiaMes($i,$inExercicio)
                                    );
            }
        break;
    
        case "ano":
        case "anual":
            $arDatas[] = array( 'stDtInicial' => "01/01/".$inExercicio
                              , 'stDtFinal' => "31/12/".$inExercicio
                              );
        break;
        
        //Mes
        default:            
            $arDatas['stDtInicial'] = "01/".str_pad($inPeriodo,2,"0",STR_PAD_LEFT)."/".$inExercicio;
            $arDatas['stDtFinal']   = SistemaLegado::retornaUltimoDiaMes($inPeriodo,$inExercicio);
        break;
    }
}

/*******************************************************************************************************/
/**** Retorna a String removendo os acentos e alguns simbolos                                       ****/
/**** $string: string para ser removida os acentos e simbolos, pode ser um array                    ****/
/**** Exemplo de uso .: removeAcentosSimbolos("áéíóçaaá");                                        ****/
/**** Exemplo de uso .: removeAcentosSimbolos($arValores['campoString']);                         ****/
/*******************************************************************************************************/
public static function removeAcentosSimbolos(&$string)
{
    //Adicionando mapa de caracteres
    $stMapaCaracteres = array( 'á' => 'a','à' => 'a','ã' => 'a','â' => 'a'
                              ,'é' => 'e','ê' => 'e'
                              ,'í' => 'i'
                              ,'ó' => 'o','ô' => 'o','õ' => 'o'
                              ,'ú' => 'u','ü' => 'u'
                              ,'ç' => 'c'
                              ,'Á' => 'A','À' => 'A','Ã' => 'A','Â' => 'A'
                              ,'É' => 'E','Ê' => 'E'
                              ,'Í' => 'I'
                              ,'Ó' => 'O','Ô' => 'O','Õ' => 'O'
                              ,'Ú' => 'U','Ü' => 'U'
                              ,'Ç' => 'C'
                              ,"'" => ''
                              ,'ª' => ''
                              ,'º' => ''
                              ,'¿' => ''
                              ,'°' => ''
                              ,'²' => ''
                              ,'³' => ''
                              ,';' => ''
                              ,'"' => ''
                              ,'ñ' => 'n'
                              ,'Ñ' => 'N'
                              ,'–' => '-'
                              ,"¨" => ''
                              ,"/" => ''
                            );
    
    //Buscando o tipo de dado que veio por parametro
    if ( is_array($string) ) {
        $stTipoDado = "array";
    }elseif ( is_object($string) ) {
        $stTipoDado = "objeto";
    }else{
        $stTipoDado = "string";
    }
    
    //De acordo com cara tipo realiza as funcoes certas
    switch ($stTipoDado) {
            case 'array':
                foreach ($string as $key => $value) {
                    $string[$key] = strtr($value, $stMapaCaracteres);
                }
            break;
            
            case 'string':
                $string = strtr($string, $stMapaCaracteres);
            break;

            default:
                return $string;
            break;
        }    
}
/*******************************************************************************************************/
/**** Formata numero do padrao do banco para o padrao brasileiro e vice e versa                     ****/
/**** Exemplo de uso .: mascaraValorBD("2.341,30",false); = 2341.30                                 ****/
/**** Exemplo de uso .: mascaraValorBD("2341.30",true); = 2.341,30                                  ****/
/*******************************************************************************************************/
public static function formataValorDecimal($value, $boToBR = false)
{    
    if($boToBR)
        $value = number_format($value,2,',','.');
    else
        $value = str_replace(',','.',str_replace('.','',$value));
    return $value;
}


/************************************************************************************/
/**** Exibe mensagem no topo do sistema com fundo vermelho e letras em cor branca ***/
/************************************************************************************/
public static function exibeAlertaTopo($data)
{
    echo '<div style="background:#FF0000; padding: 10px; color: #FFF; text-align:left;">'.$data.'</div>';
    return true;
}


}//END CLASS