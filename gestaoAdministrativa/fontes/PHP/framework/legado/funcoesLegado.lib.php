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
*/

include_once 'dataBaseLegado.class.php';
//dataToBr('2003-02-24'); retorna 24/02/2003
function dataToBr($data)
{
$data = substr($data,0,10);
$data = str_replace("/","-","$data");
$data = str_replace(".","-","$data");
$ArrData = explode("-", $data);
$data = $ArrData[2] . "/" . $ArrData[1] . "/" . $ArrData[0];
return $data;
}

/**************************************************************************/
/**** Data formaato Brasileiro para formato SQL                         ***/
/**************************************************************************/
//dataToSql('24/12/2003'); retorna 2003-24-12
function dataToSql($data)
{
$data = str_replace("/","-","$data");
$data = str_replace(".","-","$data");
$ArrData = explode("-", $data);
$data = trim($ArrData[2]) . "-" . trim($ArrData[1]) . "-" . trim($ArrData[0]);
return $data;
}

/***********************************************************************************
 Entra com o valor de um timestamp e retorna a data ou a hora no formato Brasileiro
                        *** Ricardo Lopes ***
************************************************************************************
 Exemplo:
    timestampToBr('2003-05-28 18:08:56',d); retorna 28/05/2003
    timestampToBr('2003-05-28 18:08:56',h); retorna 18:08
    timestampToBr('2003-05-28 18:08:56',hs); retorna 18:08:56
***********************************************************************************/
function timestampToBr($time,$tipo="d")
{
    $aux = explode(" ",$time);
    $data = dataToBr($aux[0]);
    $hs = $aux[1];

    $h = explode(":",$hs);
    $hora = $h[0].":".$h[1];

    if($tipo=="d")

        return $data;
    if($tipo=="h")

        return $hora;
    if($tipo=="hs")

        return $hs;
}

/*****************************************************************************
Retorna a data passada como parâmetro no formato extenso
Ex. a data passada como parâmetro (05/01/2005), DIAEXTENSO('05/01/2005'); retorna
return = Aos cinco dias de janeiro de dois mil e cinco
******************************************************************************/
function DIAEXTENSO($data)
{
    // Autor: Vilson Cristiano Gärtner - vgartner@univates.br
    // Modificado: João Rafael Tissot - suporte@cnm.org.com.br
    if ($data) {
        $Dia = substr($data, 0, 2);
        $Mes = substr($data, 3, 2);
        $Ano = substr($data, 6, 4);

        $Extenso = "AOS ";

        switch ($Dia) {
            case "01":        $Extenso = "AO PRIMEIRO DIA ";        break;
            case "02":        $Extenso .= "DOIS ";        break;
            case "03":        $Extenso .= "TRêS ";        break;
            case "04":        $Extenso .= "QUATRO ";        break;
            case "05":        $Extenso .= "CINCO ";        break;
            case "06":        $Extenso .= "SEIS ";        break;
            case "07":        $Extenso .= "SETE ";        break;
            case "08":        $Extenso .= "OITO ";        break;
            case "09":        $Extenso .= "NOVE ";        break;
            case "10":        $Extenso .= "DEZ ";        break;
            case "11":        $Extenso .= "ONZE ";        break;
            case "12":        $Extenso .= "DOZE ";        break;
            case "13":        $Extenso .= "TREZE ";        break;
            case "14":        $Extenso .= "QUATORZE ";        break;
            case "15":        $Extenso .= "QUINZE ";        break;
            case "16":        $Extenso .= "DEZESSEIS ";        break;
            case "17":        $Extenso .= "DEZESSETE ";        break;
            case "18":        $Extenso .= "DEZOITO ";        break;
            case "19":        $Extenso .= "DEZENOVE ";        break;
            case "20":        $Extenso .= "VINTE ";        break;
            case "21":        $Extenso .= "VINTE E UM ";        break;
            case "22":        $Extenso .= "VINTE E DOIS ";        break;
            case "23":        $Extenso .= "VINTE E TRêS ";        break;
            case "24":        $Extenso .= "VINTE E QUATRO ";        break;
            case "25":        $Extenso .= "VINTE E CINCO ";        break;
            case "26":        $Extenso .= "VINTE E SEIS ";        break;
            case "27":        $Extenso .= "VINTE E SETE ";        break;
            case "28":        $Extenso .= "VINTE E OITO ";        break;
            case "29":        $Extenso .= "VINTE E NOVE ";        break;
            case "30":        $Extenso .= "TRINTA ";        break;
            case "31":        $Extenso .= "TRINTA E UM ";        break;
            default:        $Extenso = "DIA INVÁLIDO. INFORME DD/MM/AAAA ";
            break;
        }

        if ($Dia > "01") {
            $Extenso .= "DIAS ";}

            switch ($Mes) {
                case "01" : $Extenso .= "DE JANEIRO DE ";   break;
                case "02" : $Extenso .= "DE FEVEREIRO DE "; break;
                case "03" : $Extenso .= "DE MARÇO DE ";     break;
                case "04" : $Extenso .= "DE ABRIL DE ";     break;
                case "05" : $Extenso .= "DE MAIO DE ";      break;
                case "06" : $Extenso .= "DE JUNHO DE ";     break;
                case "07" : $Extenso .= "DE JULHO DE ";     break;
                case "08" : $Extenso .= "DE AGOSTO DE ";    break;
                case "09" : $Extenso .= "DE SETEMBRO DE ";  break;
                case "10" : $Extenso .= "DE OUTUBRO DE ";   break;
                case "11" : $Extenso .= "DE NOVEMBRO DE ";  break;
                case "12" : $Extenso .= "DE DEZEMBRO DE ";  break;
                default : $Extenso .= "MÊS INVÁLIDO. INFORME DD/MM/AAAA ";
                break;
            }

            switch ($Ano) {
                case "1998" : $Extenso = $Extenso . "UM MIL NOVECENTOS E NOVENTA E OITO"; break;
                case "1999" : $Extenso = $Extenso . "UM MIL NOVECENTOS E NOVENTA E NOVE"; break;
                case "2000" : $Extenso = $Extenso . "DOIS MIL";                           break;
                case "2001" : $Extenso = $Extenso . "DOIS MIL E UM";                      break;
                case "2002" : $Extenso = $Extenso . "DOIS MIL E DOIS";                    break;
                case "2003" : $Extenso = $Extenso . "DOIS MIL E TRêS";                    break;
                case "2004" : $Extenso = $Extenso . "DOIS MIL E QUATRO";                  break;
                case "2005" : $Extenso = $Extenso . "DOIS MIL E CINCO";                   break;
                case "2006" : $Extenso = $Extenso . "DOIS MIL E SEIS";                    break;
                case "2007" : $Extenso = $Extenso . "DOIS MIL E SETE";                    break;
                case "2008" : $Extenso = $Extenso . "DOIS MIL E OITO";                    break;
                case "2009" : $Extenso = $Extenso . "DOIS MIL E NOVE";                    break;
                case "2010" : $Extenso = $Extenso . "DOIS MIL E DEZ";                     break;
                case "2011" : $Extenso = $Extenso . "DOIS MIL E ONZE";                    break;
                case "2012" : $Extenso = $Extenso . "DOIS MIL E DOZE";                    break;
                case "2013" : $Extenso = $Extenso . "DOIS MIL E TREZE";                   break;
                case "2014" : $Extenso = $Extenso . "DOIS MIL E QUATORZE";                break;
                case "2015" : $Extenso = $Extenso . "DOIS MIL E QUINZE";                  break;
                case "2016" : $Extenso = $Extenso . "DOIS MIL E DEZESSEIS";               break;
                case "2017" : $Extenso = $Extenso . "DOIS MIL E DEZESSETE";               break;
                case "2018" : $Extenso = $Extenso . "DOIS MIL E DEZOITO";                 break;
                case "2019" : $Extenso = $Extenso . "DOIS MIL E DEZENOVE";                break;
                case "2020" : $Extenso = $Extenso . "DOIS MIL E VINTE";                   break;
                default : $Extenso = $Extenso . "ANO INVÁLIDO OU NÃO SUPORTADO. INFORME DD/MM/AAAA ";
                break;
            }

            return $Extenso;
    } else

    return $data;
}

/**************************************************************************/
/**** Retorna por extenso                                               ***/
/**************************************************************************/
//Autor: Jorge Ribarr Qui Mar 13 14:13:08 BRT 2003
//print dataExtenso('2003-03-24',true);  Segunda-feira, 24 de Março de 2003.
//print dataExtenso('2003-03-24');       24 de Março de 2003.
function dataExtenso($sData,$bComDiaSemana=false)
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
/**** Retorna true se a data for valida e false caso contrario          ***/
/**************************************************************************/
//Autor: Cassiano de Vasconcellos Ferreira
function verificaData($stData = "")
{
    if ($stData) {
        $boErro = false;
        list( $iDia, $iMes, $iAno ) = preg_split( '/[^0-9a-zA-Z]/', $stData );
        if ($iDia < 1 || $iDia > 31) {
            $boErro = true;
        }
        if ($iMes < 1 || $iMes > 12) {
            $boErro = true;
        }
        if ($iMes == 4 || $iMes == 6 || $iMes == 9 || $iMes == 11) {
            if ($iDia > 30) {
                $boErro = true;
            }
        }
        if ($iMes == 2) {
            $inBissexto = $iAno % 4;
            if ($inBissexto != 0 && $iDia > 28) {
                $boErro = true;
            }
            if ($inBissexto == 0 && $iDia > 29) {
                $boErro = true;
            }
        }
        if ($boErro) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

/**************************************************************************/
/**** Retorna um número com digito verificador do módulo 11             ***/
/**************************************************************************/
//Autor: Jorge Ribarr Qui Mar 13 16:12:51 BRT 2003
//print geraDigito(795);  retorna 795-3
function geraDigito($sValor)
{
    $sValor = trim($sValor);
    $iTam   = strlen($sValor);
    $iSoma  = 0;
    if ($iTam>0) {
        for ($iConta=1; $iConta<=$iTam; $iConta++) {
            $iPos  = $iTam - $iConta;
            $iChar = (int) substr($sValor, $iPos, 1);
            $iSoma = $iSoma + ($iChar * ($iConta + 1));
        }
        $iAux = $iSoma % 11;
        $iAux = 11 - $iAux;
        if ($iAux>=10) {
            $iDig = 0;
        } else {
            $iDig = $iAux;
        }
    }
    $sAux = (int) $sValor."-".(int) $iDig;

    return $sAux;
}

/**************************************************************************
 Verifica se o valor digitado possui o dígito correto
 Exemplo: verificaDigito("1-9") -> retorna true
          verificaDigito("1-4") -> retorna false
***************************************************************************/
function verificaDigito($valor)
{
    //Separa o dígito do valor digitado
    $valor = explode("-",$valor);
    $val = $valor[0];
    $dig = $valor[1];
    //Gera o dígito correto
    $digito = geraDigito($val);
    $digito = explode("-",$digito);
    //Verifica se o dígito fornecido pelo usuário está correto
    if ($dig==$digito[1]) {
        return true;
    } else {
        return false;
    }
}

/**************************************************************************/
/**** Retorna numeros no formato cpf                                    ***/
/**************************************************************************/
//Autor: Jorge Ribarr Seg Mar 10 15:33:28 BRT 2003
//print numeroToCpf(12345678912);  retorna 123.456.789-12
function numeroToCpf($iNumeros)
{
    $sNum = (string) $iNumeros;
    if (strlen($sNum)==11) {
        $sCpf = substr($sNum,0,3).".".substr($sNum,3,3).".".substr($sNum,6,3)."-".
                substr($sNum,9);
    } else {
        $sCpf = "$sNum sem 11 dígitos";
    }

    return $sCpf;
}

/**************************************************************************/
/**** Retorna numeros no formato cnpj                                   ***/
/**************************************************************************/
//Autor: Jorge Ribarr Seg Mar 10 15:37:29 BRT 2003
//print numeroToCpf(12345678000112);  retorna 12.345.678/0001-12
function numeroToCnpj($iNumeros)
{
    $sNum  = (string) $iNumeros;
    if (strlen($sNum)==14) {
        $sCnpj = substr($sNum,0,2).".".substr($sNum,2,3).".".substr($sNum,5,3)."/".
                 substr($sNum,8,4)."-".substr($sNum,12);
    } else {
        $sCnpj = "$sNum sem 14 dígitos";
    }

    return $sCnpj;
}

/**************************************************************************/
/**** Retorna telefone formatado                                        ***/
/**************************************************************************/
//Autor: Ricardo Lopes Ter Mar 18 10:14:29 BRT 2003
//print formataFone(5199990000);  retorna (51)9999-0000
function formataFone($iFone)
{
    $sNum  = (string) $iFone;
    if (strlen($sNum)==10) {
        $sFone = "(".substr($sNum,0,2).")".substr($sNum,2,4)."-".substr($sNum,6,4);
    } elseif (strlen($sNum)==9) {
        $sFone = "(".substr($sNum,0,2).")".substr($sNum,2,3)."-".substr($sNum,5,4);
    } else {
        $sFone = $sNum;
    }

    return $sFone;
}

/**************************************************************************/
/**** Retorna cep formatado                                             ***/
/**************************************************************************/
//Autor: Ricardo Lopes Ter Mar 18 10:37:36 BRT 2003
//print formataCep(90123000);  retorna 90123-000
function formataCep($iCep)
{
    $sNum  = (string) $iCep;
    if (strlen($sNum)==8) {
        $sCep = substr($sNum,0,5)."-".substr($sNum,5,3);
    } else {
        $sCep = $sNum;
    }

    return $sCep;
}

/**************************************************************************/
/**** Retorna hora atual com ou sem pontos e com ou sem milisegundos    ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 10:26:53 BRT 2003
//print agora();           retorna 14:25:36
//print agora(true);       retorna 142536
//print agora(false,true); retorna 14:25:36.789
//print agora(true,true);  retorna 142536789
function agora($bSemPontos=false, $bMilisec=false)
{
   if ($bSemPontos) {
      $sAux = "His";
   } else {
      $sAux = "H:i:s";
   }
   $sAgora = date($sAux,time());
   if ($bMilisec) {
      list($sMilisec, $sSec) = explode(" ",microtime());
      $sMilisec = substr($sMilisec,1,4);
      if ($bSemPontos) {
          $sMilisec = substr($sMilisec,1);
      }
      $sAgora .= $sMilisec;
   }

   return $sAgora;
}

/**************************************************************************/
/**** Retorna data atual com barras ou com hífens                       ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 10:27:00 BRT 2003
//print hoje();     retorna 25/02/2003
//print hoje(true); retorna 2003-02-25
//print hoje(false,false); retorna 20030225
function hoje($bComHifem=false,$bComBarra=true)
{
   if ($bComHifem) {
      $sAux = "Y-m-d";
   } else {
      if ($bComBarra) {
         $sAux = "d/m/Y";
      } else {
         $sAux = "Ymd";
      }
   }

   return date($sAux,time());
}

/**************************************************************************/
/**** Retorna um número de identificação de uma tabela                  ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 17:49:04 BRT 2003
//$iID = pegaID('num_cgm','cgm'); retorna o maior código +1
function pegaID($sChave, $sTabela, $sWhere="")
{
   $DBx = new dataBaseLegado;
   $DBx->abreBD();
   $sSQL = "select max($sChave) as ultima from $sTabela $sWhere";
   $DBx->abreSelecao($sSQL);
   $DBx->vaiPrimeiro();
   if (!$DBx->eof()) {
      $iID = (int) $DBx->pegaCampo('ultima');
   }
   $iID = $iID + 1;

   return $iID;
}

/**************************************************************************/
/**** Retorna um combo de qualquer tabela                               ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 17:53:15 BRT 2003
//$sCbox = montaComboGenerico('iIDUser', 'usuarios', 'iduser', 'username', $iIDUser,
//                            'onClick="javascript:mudouUser();"', "where userlevel='sa'", true);
function montaComboGenerico($sCampo, $sTabela, $sChave, $sTitulo, $sFiltro,
                            $sComplemento="", $sWhere="", $bMostraX=False,
                            $bOrderDesc=false,$bRetornaChaveTitulo=false,$query="",$sNomeX="Selecione") {
    $sCombo = "";
    $sFiltro = trim($sFiltro);
    if ($bOrderDesc==false) {
        $sOrder = $sTitulo;
    } else {
        $sOrder = $sTitulo." desc";
    }
    $dbCGen = new dataBaseLegado;
    $dbCGen->abreBD();
    if (strlen($query) == 0) {
        $sSQL = "select $sChave, $sTitulo from $sTabela $sWhere order by $sOrder";
    } else {
        $sSQL = $query;
    }
    $dbCGen->abreSelecao($sSQL);
    $dbCGen->vaiPrimeiro();
    $sCombo .= '
    <select name="'.$sCampo.'" '.$sComplemento.'>';
    if ($bMostraX) {
        $sCombo .= '
        <option value="XXX" style="color:#ff0000">'.$sNomeX.'</option>';
    }
    while (!$dbCGen->eof()) {
        $sCha    = trim((string) $dbCGen->pegaCampo($sChave));
        if ($sCha==$sFiltro) {
            $sAux = "selected";
        } else {
            $sAux = "";
        }
        $aTitulos = explode(",",$sTitulo);
        $sTit = "";
        while (list ($key, $val) = each ($aTitulos)) {
            if (strlen($sTit)>0) {
                $sTit = $sTit." - ";
            }
            $sTit = $sTit.trim($dbCGen->pegaCampo(trim($val)));
        }
        if (!$bRetornaChaveTitulo) {
            $sCombo .= '
            <option title="'.$sTit.'" '.$sAux.' value="'.$sCha.'">'.$sTit.'</option>';
        } else {
            $sCombo .= '
            <option title="'.$sTit.'" '.$sAux.' value="'.$sCha.','.$sTit.'">'.$sTit.'</option>';
        }
        $dbCGen->vaiProximo();
    }
    $sCombo .= '
    </select>';
    $dbCGen->limpaSelecao();
    $dbCGen->fechaBD();

    return $sCombo;
}

/**************************************************************************/
/**** Retorna um dado de qualquer tabela                                ***/
/**************************************************************************/
//Autor: Jorge Ribarr Ter Fev 25 18:21:34 BRT 2003
function pegaDado($sDado,$sTabela,$sWhere)
{
    $sRes = "";
    $DBCon = new dataBaseLegado;
    $DBCon->abreBD();
    $sSQL = "select $sDado from $sTabela $sWhere";
    //print $sSQL; exit;
    $DBCon->abreSelecao($sSQL);
    $DBCon->vaiPrimeiro();
    if (!$DBCon->eof()) {
        $sRes = $DBCon->pegaCampo($sDado);
    }
    $DBCon->limpaSelecao();
    $DBCon->fechaBD();

    return $sRes;
}

/**************************************************************************
 Informa a query que será executada e o campo a ser retornado
 Exemplo:
   $codMinimo = pegaValor("Select min(cod) as minimo From Tabela","minimo");
/**************************************************************************/
//Autor: Ricardo Lopes Qua Abr  9 13:41:44 UTC 2003
function pegaValor($query,$campo)
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

/**************************************************************************/
/**** Imprime todos os dados de uma variável - utilizada para debug     ***/
/**************************************************************************/
//Autor: Jorge Ribarr Sex Fev 28 10:54:16 BRT 2003
function mostraVar($vVariavel)
{
    print "<div align='left'><pre>\n";
    print_r($vVariavel);
    print "\n</pre></div>";
}

/**************************************************************************/
/**** Pega valores do arquivo de configuraçãoes                         ***/
/**************************************************************************/
//Autor: Jorge Ribarr Qua Mar  5 10:12:21 BRT 2003
function pegaConfiguracao($sParametro,$iCodModulo=2,$sExercicio="")
{
    $bdConf = new dataBaseLegado();
    $bdConf->abreBD();
    $sSQL = "SELECT cod_modulo
                  , parametro, valor
               FROM administracao.configuracao
          WHERE cod_modulo =".$iCodModulo."
                AND parametro ='".$sParametro."'";

    if ($sExercicio != "") {
       $sSQL .= " AND exercicio = '".$sExercicio."'";
    } else {
        $sSQL .= " and exercicio <= '".Sessao::getExercicio()."'";
        $sSQL .= " order by exercicio desc limit 1 ";
    }
    $bdConf->abreSelecao($sSQL);
    $sValor = $sParametro." não encontrado para o módulo ".$iCodModulo;
    if (!$bdConf->eof()) {
        while ( !$bdConf->eof() ) {
            $sValor = trim($bdConf->pegaCampo('valor'));
            $bdConf->vaiproximo();
        }
    }
    $bdConf->fechaBD();

    return $sValor;
}

/**************************************************************************/
/**** Transforma o nome de um stado em sua sigla                        ***/
/**** Autor: Leonardo Tremper                                           ***/
/**************************************************************************/
/*
Exmplo
$estado = 'São Paulo';
$sigla = estado_sigla($estado);
echo $estado." - ".$sigla;
*/
function estado_sigla($estado)
{
    $sigla == '';
    if ($estado == 'Acre') {$sigla = 'AC';} elseif ($estado == 'Alagoas') {$sigla = 'AL';} elseif ($estado == 'Amazonas') {$sigla = 'AM';} elseif ($estado == 'Amapá') {$sigla = 'AP';} elseif ($estado == 'Bahia') {$sigla = 'BA';} elseif ($estado == 'Ceará') {$sigla = 'CE';} elseif ($estado == 'Distrito Federal') {$sigla = 'DF';} elseif ($estado == 'Espírito Santo') {$sigla = 'ES';} elseif ($estado == 'Fernando de Noronha') {$sigla = 'FN';} elseif ($estado == 'Goiás') {$sigla = 'GO';} elseif ($estado == 'Maranhão') {$sigla = 'MA';} elseif ($estado == 'Minas Gerais') {$sigla = 'MG';} elseif ($estado == 'Mato Grosso do Sul') {$sigla = 'MS';} elseif ($estado == 'Mato Grosso') {$sigla = 'MT';} elseif ($estado == 'Pará') {$sigla = 'PA';} elseif ($estado == 'Paraíba') {$sigla = 'PB';} elseif ($estado == 'Pernambuco') {$sigla = 'PE';} elseif ($estado == 'Piauí') {$sigla = 'PI';} elseif ($estado == 'Paraná') {$sigla = 'PR';} elseif ($estado == 'Rio de Janeiro') {$sigla = 'RJ';} elseif ($estado == 'Rio Grande do Norte') {$sigla = 'RN';} elseif ($estado == 'Rondônia') {$sigla = 'RO';} elseif ($estado == 'Roraima') {$sigla = 'RR';} elseif ($estado == 'Rio Grande do Sul') {$sigla = 'RS';} elseif ($estado == 'Santa Catarina') {$sigla = 'SC';} elseif ($estado == 'Sergipe') {$sigla = 'SE';} elseif ($estado == 'São Paulo') {$sigla = 'SP';} elseif ($estado == 'Tocantins') {$sigla = 'TO';}

return $sigla;
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
function extenso($valor=0, $maiusculas=false)
{
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
"quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
"sete", "oito", "nove");

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

        $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
$ru) ? " e " : "").$ru;
        $t = count($inteiro)-1-$i;
        $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")$z++; elseif ($z > 0) $z--;
        if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
        if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

         if (!$maiusculas) {
                          return($rt ? $rt : "zero");
         } else {
                          return (ucwords($rt) ? ucwords($rt) : "Zero");
         }

}

/**************************************************************************/
/**** Retira os acentos de uma palavra                                  ***/
/**** Autor: Leonardo Tremper                                           ***/
/**************************************************************************/
function tiraAcentos($palavra)
{
$palavra = preg_replace("/[áàâãª]/","a",$palavra);
$palavra = preg_replace("/[ÁÀÂÃ]/","A",$palavra);
$palavra = preg_replace("/[éèê]/","e",$palavra);
$palavra = preg_replace("/[ÉÈÊ]/","E",$palavra);
$palavra = preg_replace("/[óòôõº]/","o",$palavra);
$palavra = preg_replace("/[ÓÒÔÕ]/","O",$palavra);
$palavra = preg_replace("/[úùû]/","u",$palavra);
$palavra = preg_replace("/[ÚÙÛ]/","U",$palavra);
$palavra = str_replace("ç","c",$palavra);
$palavra = str_replace("Ç","C",$palavra);

return $palavra;

}

/**************************************************************************/
/**** Redireciona que redireciona o frame telaPrincipal                 ***/
/**** Autor: Ricardo Lopes - 10/12/2003                                 ***/
/**************************************************************************/
/* Exemplo:    mudaTelaPrincipal("incluiNovo.php"); */
function mudaTelaPrincipal($location="")
{
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                mudaTelaPrincipal("'.$location.'");
           </script>';
}

/**************************************************************************/
/**** Chama o script com mensagem de alerta e redireciona               ***/
/**** Autor: Ricardo Lopes Seg Abr  7 15:08:06 UTC 2003                 ***/
/**************************************************************************/
/* Exemplo:
    alertaAviso("incluiNovo.php","Usuário","incluir","aviso"); */
function alertaAviso($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao='', $caminho="")
{
    //print "<br> id: ".Sessao::getId()."<br>";
    //echo $caminho."<br>";
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                mudaTelaPrincipal("'.$location.'");
           </script>';
}
function alertaAviso2($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao='', $caminho="")
{
    //print "<br> id: ".Sessao::getId()."<br>";
    //echo $caminho."<br>";
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                parent.window.location.replace("'.$location.'");
           </script>';
}
function alertaAvisoPopUp($location="",$objeto="",$tipo="n_incluir",$chamada="erro", $_sessao='', $caminho="")
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
/**************************************************************************/
/**** Chama o pop-up com mensagem de alerta e NÃO redireciona           ***/
/**** Autor: Ricardo Lopes Seg Abr  7 15:10:17 UTC 2003                 ***/
/**************************************************************************/
/* Exemplo:
    exibeAviso("Usuário","alterar","aviso"); */
function exibeAviso($objeto="",$tipo="n_incluir",$chamada="erro")
{
    print '<script type="text/javascript">
                alertaAviso("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'");
           </script>';
}

/**************************************************************************
 Entra com o local no formato 1.1.1.1.1, verifica se este é um local válido
 e retorna o local em forma de vetor
/**************************************************************************/
function validaLocal($local,$exercicio)
{
    $local = explode(".",$local);
    if (
        $local[0] == '0' AND
        $local[1] == '0' AND
        $local[2] == '0' AND
        $local[3] == '0' AND
        $local[4] == '0'
    ) {
        $vetLocal = false;
    } else {
        $vetLocal[codOrgao] = $local[0];
        $vetLocal[codUnidade] = $local[1];
        $vetLocal[codDpto] = $local[2];
        $vetLocal[codSetor] = $local[3];
        $vetLocal[codLocal] = $local[4];
        $sql = "Select nom_local From administracao.local
                Where cod_local = '".$vetLocal[codLocal]."'
                And cod_setor = '".$vetLocal[codSetor]."'
                And cod_departamento = '".$vetLocal[codDpto]."'
                And cod_unidade = '".$vetLocal[codUnidade]."'
                And cod_orgao = '".$vetLocal[codOrgao]."'
                And ano_exercicio = '".$exercicio."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if ($conn->numeroDeLinhas==0) {
                $vetLocal = false;
            } else {
                $vetLocal[nomLocal] = $conn->pegaCampo("nom_local");
            }
        $conn->limpaSelecao();
    }

    return $vetLocal;
}//Fim da function validaLocal

/**************************************************************************
 Entra com o setor no formato 1.1.1.1, verifica se este é um setor válido
 e retorna o setor em forma de vetor
/**************************************************************************/
function validaSetor($setor,$exercicio)
{
    $setor = explode(".",$setor);
        $vetSetor[codOrgao] = $setor[0] ? $setor[0] : 0;
        $vetSetor[codUnidade] = $setor[1] ? $setor[1] : 0;
        $vetSetor[codDpto] = $setor[2] ? $setor[2] : 0;
        $vetSetor[codSetor] = $setor[3] ? $setor[3] : 0;
    $sql = "Select nom_setor From administracao.setor
            Where cod_setor = '".$vetSetor[codSetor]."'
            And cod_departamento = '".$vetSetor[codDpto]."'
            And cod_unidade = '".$vetSetor[codUnidade]."'
            And cod_orgao = '".$vetSetor[codOrgao]."'
            And ano_exercicio = '".$exercicio."' ";
    //echo $sql."<br>";
    //Pega os dados encontrados em uma query
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($sql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
        if ($conn->numeroDeLinhas==0) {
            $vetSetor = false;
        } else {
            $vetSetor[nomSetor] = $conn->pegaCampo("nom_setor");
        }
    $conn->limpaSelecao();

    return $vetSetor;
}// Fim da function validaSetor

/**************************************************************************/
/**** Verifica se já  existe um registro de nome semalhante em uma tabela */
/**************************************************************************/
//Autor: Leonardo Tremper Qua Mai  7 10:12:18 UTC 2003
//$iCompara = comparaValor("nom_funcao", $nomFuncao, FUNCAO);
function comparaValor($sChave, $sValor, $sTabela, $sWhere="", $sCase=0)
{
   $DBx = new dataBaseLegado;
   $DBx->abreBD();
   if ($sCase == 0)
   $sSQL = "select $sChave as total from $sTabela where $sChave = '$sValor' $sWhere";
   else
   $sSQL = "select $sChave as total from $sTabela where lower($sChave) = lower('$sValor') $sWhere";
   //echo $sSQL."<br>";
   $DBx->abreSelecao($sSQL);
   $DBx->fechaBD();
   $DBx->vaiPrimeiro();
   if ($DBx->numeroDeLinhas == 0)
   return true;
   else
   return false;
}

/***************************************************************************
/**** Gera o código html com o botão OK (com a função Salvar) e botão limpar
 Autor: Ricardo Lopes 14/05/2003
 Exemplo: <td class=field colspan=2>
            <?php geraBotaoOk(); ?>
          </td>
 Retorna: <table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>
            <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();">
            &nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px">
            </td>
            <td class="fieldright_noborder"><b>* Campos Obrigatórios</b>
            </td></tr></table>
/**************************************************************************/
function geraBotaoOk($botaoOk=1,$botaoLimpar=1,$campos=1,$botaoCancelar=0)
{
    global $tabindex_ok;
    global $tabindex_limpar;
    global $tabindex_cancelar;
  $tabIndexOk = '';
  $tabIndexLimpar = '';
    if ($tabindex_ok != '') {
        $tabIndexOk = ' tabindex="'.$tabindex_ok.'" ';
    }
    if ($tabindex_limpar != '') {
        $tabIndexLimpar = ' tabindex="'.$tabindex_limpar.'" ';
    }
    if ($tabindex_cancelar != '') {
        $tabIndexCancelar = ' tabindex="'.$tabindex_cancelar.'" ';
    }
    $html = '<table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>';
    if($botaoOk==1)
        $html .= '<input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();" '.$tabIndexOk.'>';
    if($botaoCancelar==1)
        $html .= '&nbsp;<input type="button" name="cancelar" value="Cancelar" style="width: 70px" onClick="Cancela();" '.$tabIndexCancelar.'>';
    if($botaoLimpar==1)
        $html .= '&nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px" '.$tabIndexLimpar.'>';
    $html .= '</td><td class="fieldright_noborder">';
    if($campos==1)
        $html .= '<b>* Campos obrigatórios</b>';
    $html .= '</td></tr></table>';
    print $html;
}

/**************************************************************************/
function geraBotaoOk2($botaoOk=1,$botaoLimpar=1,$campos=1,$botaoCancelar=0)
{
    $html = '<table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>';
    if($botaoOk==1)
        $html .= '<input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();">';
    if($botaoCancelar==1)
        $html .= '&nbsp;<input type="button" name="cancelar" value="Cancelar" style="width: 70px" onClick="Cancela();">';
    if($botaoLimpar==1)
        $html .= '&nbsp;<input type="button" name="limpar" value="Limpar" style="width: 60px" onClick="Limpar();">';
    $html .= '</td><td class="fieldright_noborder">';
    if($campos==1)
        $html .= '<b>* Campos obrigatórios</b>';
    $html .= '</td></tr></table>';
    print $html;
}

/***************************************************************************
/**** Gera o código html com o botão OK como submit (com a função Salvar) e botão limpar
 Autor: Ricardo Lopes 14/05/2003
 Exemplo: <td class=field colspan=2>
            <?php geraBotaoOk(); ?>
          </td>
 Retorna: <table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>
            <input type="submit" name="ok" value="OK" style="width: 60px" onClick="Salvar();">
            &nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px">
            </td>
            <td class="fieldright_noborder"><b>* Campos Obrigatórios</b>
            </td></tr></table>
/**************************************************************************/
function geraBotaoOk3($botaoOk=1,$botaoLimpar=1,$campos=1,$botaoCancelar=0)
{
    $html = '<table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>';
    if($botaoOk==1)
        $html .= '<input type="submit" name="ok" value="OK" style="width: 60px" onClick="Salvar();">';
    if($botaoCancelar==1)
        $html .= '&nbsp;<input type="button" name="cancelar" value="Cancelar" style="width: 70px" onClick="Cancela();">';
    if($botaoLimpar==1)
        $html .= '&nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px">';
    $html .= '</td><td class="fieldright_noborder">';
    if($campos==1)
        $html .= '<b>* Campos obrigatórios</b>';
    $html .= '</td></tr></table>';
    print $html;
}

function geraBotaoOk4($botaoOk=1,$botaoLimpar=1,$campos=1,$botaoCancelar=0)
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    $obFormulario = new Formulario;

    $obBtnOk = new OK;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName( "Limpar" );
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->obEvento->setOnClick( "location.reload(true);" );
    
    $obFormulario->defineBarra (array($obBtnOk, $obBtnLimpar));

    $obFormulario->montaInnerHtml();
    $html = $obFormulario->getHTML();
    echo $html;
}

/***************************************************************************
/**** Gera o código html com o botão OK (com a função Salvar) e botão Cancelar
 Autor: Ricardo Lopes 14/05/2003
 Exemplo: <td class=field colspan=2>
            <?php geraBotaoOk(); ?>
          </td>
 Retorna: <table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>
            <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();">
            &nbsp;<input type="button" name="cancelar" value="Cancelar" style="width: 70px" onClick="Cancela();">
            </td>
            <td class="fieldright_noborder"><b>* Campos Obrigatórios</b>
            </td></tr></table>
/**************************************************************************/
function geraBotaoAltera()
{
    geraBotaoOk(1,0,1,1);
}

/***************************************************************************
/**** Gera o código html com o campo data e o botao que chama o calendario
Autor: Cassiano de Vasconcellos Ferreira 02/12/2003
Exemplo:
    <?php geraCampoData("dtEmissaoRg", $dtEmissaoRg1, true, "onKeyUp=\"return autoTab(this, 10, event);\"" );?>
Retorna:
    <input type="text" maxlength="10" size="10" name="dtEmissaoRg" value="" readonly onKeyUp="return autoTab(this, 10, event);">
    <a href="javascript: MostraCalendario('frm','dtEmissaoRg','');"><img src="../../images/calendario.gif" border="0"></a>
/**************************************************************************/
function geraCampoData($sNome = "data", $default = "" , $boReadOnly = true, $sFuncao = "")
{
    $sHtml  = "\n<input type=\"text\" maxlength=\"10\" size=\"10\" name=\"$sNome\"";
    $sHtml .= " value=\"$default\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    $sHtml .= "<a href=\"javascript: MostraCalendario('frm','$sNome','Sessao::getId()');\">";
    $sHtml .= "<img src=\"../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif\" align='absmiddle' border=\"0\"></a>";
    echo $sHtml;
}

/***************************************************************************
/**** Gera o código html com o campo data e o botao que chama o calendario
Autor: Fernando Zank Correa Evangelista 17/10/2005
Exemplo:
    <?php geraCampoData("dtEmissaoRg", $dtEmissaoRg1, true, "onKeyUp=\"return autoTab(this, 10, event);\"" );?>
Retorna:
    <input type="text" maxlength="10" size="10" name="dtEmissaoRg" value="" readonly onKeyUp="return autoTab(this, 10, even
t);">
    <a href="javascript: MostraCalendario('frm','dtEmissaoRg','');"><img src="../../images/calendario.gif" border="0"></a>
/**************************************************************************/
function geraCampoData2($label, $sNome = "data", $default = "" , $boReadOnly = true, $sFuncao = "",$title,$title2='')
{
    if ($title2 == '')
        $title2 = 'Buscar data';
    $sHtml = "<tr><td class='label' title=\"$title.\">$label</td> <td class='field'>";

    $sHtml .= "\n<input type=\"text\" maxlength=\"10\" size=\"10\" name=\"$sNome\"";
    $sHtml .= " value=\"$default\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    $sHtml .= "<a href=\"javascript: MostraCalendario('frm','$sNome','Sessao::getId()');\">";
    $sHtml .= "<img src=\"../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif\" border=\"0\" title= \"$title2\" align=\"absmiddle\"></a>";
    $sHtml .= "</td></tr> ";

    echo $sHtml;
}

function geraCampoPeriodo($label, $sNome = "dataInicial", $default = "" ,$sNome2 = "dataFinal", $default2 = "",  $boReadOnly = true, $sFuncao = "",$title, $title2, $boPopup ='true')
{
    $sHtml = "<tr><td class='label'title=\"Informe o período.\"  >$label</td> <td class='field'>";

    $sHtml .= "\n<input type=\"text\" maxlength=\"10\" size=\"10\" name=\"$sNome\"";
    $sHtml .= " value=\"$default\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    if ($boPopup == 'true') {
        $sHtml .= "<a href=\"javascript: MostraCalendario('frm','$sNome','Sessao::getId()');\">";
        $sHtml .= "<img src=\"../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif\"  border=\"0\" title= \"$title\" align=\"absmiddle\"></a>";
    }
    $sHtml .= "&nbsp;até&nbsp;";
    $sHtml .= "\n<input type=\"text\" maxlength=\"10\" size=\"10\" name=\"$sNome2\"";
    $sHtml .= " value=\"$default2\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $data1 = $sNome.".value";
    $sHtml .="  onChange=\"javaScript: if (!validaCampoPeriodo($data1,this.value)) {alertaAviso('@Data Final('+this.value+') não pode ser inferior a Data Inicial('+$data1+')!','form','erro','".Sessao::getId()."');this.value='';}; \" ";
    $sHtml .= ">\n";

    if ($boPopup == 'true') {
        $sHtml .= "<a href=\"javascript: MostraCalendario('frm','$sNome2','Sessao::getId()');\">";
        $sHtml .= "<img src=\"../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif\"  border=\"0\" title= \"$title2\" align=\"absmiddle\"></a>";
    }
    $sHtml .= "</td></tr>";

    echo $sHtml;
}

function geraCampoDataHidden($label, $sNome = "data", $default = "" , $boReadOnly = true, $sFuncao = "",$title ,$title2="")
{
    $sHtml = "<tr><td class='label' title=\"$title.\" >$label</td> <td class='field'>";

    $sHtml .= "\n<input type=\"text\" maxlength=\"10\" size=\"10\" name=\"$sNome\"";
    $sHtml .= " value=\"$default\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    $sHtml .= "<a href=\"javascript: MostraCalendario('frm','$sNome','Sessao::getId()');\">";
    $sHtml .= "<img src=\"../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/calendario.gif\" border=\"0\" title= \"$title2\" align=\"absmiddle\"></a>";
    $sHtml .= "<input type=\"hidden\" name=\"ctrl\" value=\"1\">";
    $sHtml .= "</td></tr> ";

    echo $sHtml;
}

/*-------------------------------------------------------------
|Gera o código html com o campo moeda
|Autor: Cassiano de Vasconcellos Ferreira 21/01/2003
|Ex.:
|geraCampoMoeda( $sNome = "moeda", $tamanho = 14, $decimais = 2 );
|retorna:
|<input type="text" maxlength="14" align="right" onkeypress="return validaCharMoeda( this, event );" onkeyup="javascript: mascaraMoeda(  this, 2, event );" onBlur=" javascript: formataMoeda( this, 2, event );">
+--------------------------------------------------------------*/

function geraCampoMoeda($sNome = "moeda", $maxLength = 14, $decimais = 2, $value = "",  $boReadOnly = false, $sFuncao = "" , $size = "")
{
    if ($size == "") {
        $size = $maxLength;
    }

    $sHtml  = "\n<input type=\"text\" name=\"".$sNome."\" maxlength=\"".$maxLength."\"";
    $sHtml .= " size=\"".$size."\" align=\"right\" value=\"".$value."\"";
    $sHtml .= " onkeypress=\"return validaCharMoeda( this, event );\"";
    $sHtml .= " onkeyup=\"javascript: mascaraMoeda(  this, ".$decimais.", event );\"";
    $sHtml .= " onBlur=\"javascript: formataMoeda( this, ".$decimais.", event );\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    echo $sHtml;
}

/*-------------------------------------------------------------
|Gera o código html de um campo que só aceita numeros
|Autor: Cassiano de Vasconcellos Ferreira 21/01/2003
|Ex.:
|geraCampoInteiro( "codigo", 10, 10 );
|retorna:
|<input type="text" name="codigo" value="" size="10" maxlength="10" onKeyPress="return(isValido(this,event,'0123456789'));">
+--------------------------------------------------------------*/
function geraCampoInteiro($sNome = "inteiro", $maxLength = 10, $size = 10, $value = "",  $boReadOnly = false, $sFuncao = "")
{
    $sHtml  = "\n<input type=\"text\" name=\"".$sNome."\" maxlength=\"".$maxLength."\"";
    $sHtml .= " size=\"".$size."\" value=\"".$value."\"";
    $sHtml .= " onKeyPress=\"return(isValido(this,event,'0123456789'));\"";
    if ($boReadOnly) {
        $sHtml .= " readonly";
    }
    if ($sFuncao) {
        $sHtml .= " ".$sFuncao;
    }
    $sHtml .= ">\n";
    echo $sHtml;
}

/**************************************************************************/
/**** Retorna o valor contabil formatado                                ***/
/**************************************************************************/
//Autor: Jorge Ribarr Seg Mai 19 12:06:34 BRT 2003
//print valorContabil(1234.56);  retorna  1.234,56 D
//print valorContabil(-1234.56); retorna  1.234,56 C
function valorContabil($fValor)
{
    if ($fValor >=0) {
        $sSinal = "D";
        $iPos   = 0;
    } else {
        $sSinal = "C";
        $iPos   = 1;
    }
    $sValorContabil = number_format ($fValor, 2, ",", ".");
    $sValorContabil = substr($sValorContabil,$iPos)." ".$sSinal;

    return $sValorContabil;
}

/**************************************************************************/
/**** preenche combos de Localizacao a partir da MASCARA informada      ***/
/**************************************************************************/
//Autor: Marcelo Boezzio Paulino 04/03/2004

function preencheLocalizacao($stNomeComponente,$countNiveis,$mascLocalizacao)
{
    $obTLocalizacao = new TLocalizacao;
    $mascLocalizacao = "0.".$mascLocalizacao;
    $arLocalizacao = preg_split( "/[^a-zA-Z0-9]/",$mascLocalizacao);

    $count = count($arLocalizacao);

    // numero de Niveis total da Localizacao
    $inQuantNiveis = $countNiveis-1;

    for ($z=0; $z<$count; $z++) {
        $rsLocalizacao = new Recordset;

        // posicao do Nivel selecionado
        $inPosSelecionado = $z-1;
        $obTLocalizacao->recuperaTodos($rsLocalizacao, " WHERE cod_localizacao <> 0 and cod_superior='".$arLocalizacao[$z]."'", " ORDER BY nom_localizacao ");
        $inContador = 1;

        // se o recordeset possui registros, preenche o combo
        if ( $rsLocalizacao->getNumLinhas() > -1 ) {

            $js .= "limpaSelect(f.".$stNomeComponente.($inPosSelecionado+1).",0); \n";
            $js .= "f.".$stNomeComponente.($inPosSelecionado+1).".options[0] = new Option('Selecione','', 'selected');\n";

            while (!$rsLocalizacao->eof()) {
                $selected = "";
                $inCodLocalizacao = $rsLocalizacao->getCampo("cod_localizacao");
                $stNomLocalizacao = $rsLocalizacao->getCampo("nom_localizacao");

                if ($inCodLocalizacao == $arLocalizacao[$z+1]) {
                    $selected = "selected";
                }

                $addOptions .= "f.".$stNomeComponente.($inPosSelecionado+1).".options[$inContador] = new Option('".$stNomLocalizacao."','".$inCodLocalizacao."','".$selected."'); \n";

                $inContador++;
                $rsLocalizacao->proximo();

            }
            $js .= $addOptions;

        }
    }

    // limpa os combos que nao tem valor selecionado
    for ($x = $inPosSelecionado+1; $x < $inQuantNiveis; $x++) {
        $js .= "limpaSelect(f.".$stNomeComponente.($x+1).",0); \n";
        $js .= "f.".$stNomeComponente.($x+1).".options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

/***************************************************************************
/**** Imprime um código HTML para executar códigos de JavaScript para
      alterar elementos do Frame telaPrincipal
 Autor: Ricardo Lopes 18/07/2003
 Entra com códigos em JavaScript na variável $javaScript
/**************************************************************************/
function executaFrameOculto($javaScript)
{
    print '<html>
           <head>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.frames["telaPrincipal"].document.frm;
                var d = window.parent.frames["telaPrincipal"].document;
                var aux;
                '.$javaScript.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           </script>
           </head>
           <body onLoad="javascript:executa();">
           </body>
           </html>';
}

function executaiFrameOculto($javaScript)
{
    print '<html>
           <head>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.document.frm;
                var d = window.parent.document;
                var aux;
                '.$javaScript.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           </script>
           </head>
           <body onLoad="javascript:executa();">
           </body>
           </html>';
}

function executaWindowOpener($javaScript)
{
    print '<html>
           <head>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.parent.opener.document.frm;
                var d = window.parent.opener.document;
                var aux;
                '.$javaScript.'

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

function retornaValoresRecordSet(&$rsRecordSet, $stCampo)
{
    if (strstr($stCampo,'[') || strstr($stCampo,']')) {
        for ($inCount=0; $inCount<strlen($stCampo); $inCount++) {
            if ($stCampo[ $inCount ] == '[') $inInicial = $inCount;
            if (($stCampo[ $inCount ] == ']') && isset($inInicial) ) {
                $stOut .= $rsRecordSet->getCampo( trim( substr($stCampo,$inInicial+1,(($inCount-$inInicial)-1)) ) );
                unset($inInicial);
            }elseif( !isset($inInicial) )
                $stOut .= $stCampo[ $inCount ];
        }
    } else {
        $stOut = $rsRecordSet->getCampo( $stCampo );
    }

    return $stOut;
}

function unhtmlentities($stString)
{
    $stString = str_replace('&nbsp;',' ',$stString);
    $stString = str_replace('&gt;','>',$stString);
    $stString = str_replace('&lt;','<',$stString);

    return $stString;
}

function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);
}
/* Funções para bloquear frames     */
/* Lucas Stephanou || 01/03/2005    */
function BloqueiaFrames($boPrincipal=true,$boMenu=false)
{
    $ini = "<script type=\"text/javascript\">\r\n";
    $fim = "\r\n</script>\r\n";
    if ($boMenu ==true) {echo $ini."BloqueiaFrames(true,true);".$fim   ;}
    if ($boPrincipal==true) {echo $ini."BloqueiaFrames(true,false);".$fim   ;}
}
function LiberaFrames($boPrincipal=true,$boMenu=true)
{
    $ini = "<script type=\"text/javascript\">\r\n";
    $fim = "\r\n</script>\r\n";
    if ($boMenu ==true) {echo $ini."LiberaFrames(true,true);".$fim   ;}
    if ($boPrincipal==true) {echo $ini."LiberaFrames(true,false);".$fim   ;}

}

/**************************************************************************/
/**** Verifica se a data1 é maior que a data2                           ***/
/**** Se a data1 for maior retorna true, senão retorna false            ***/
/**** Autor: Cleisson Barboza - 03/06/2005                              ***/
/**************************************************************************/
function comparaDatas($stData1,$stData2)
{
   list( $dia1,$mes1,$ano1 ) = explode( '/', $stData1 );
   list( $dia2,$mes2,$ano2 ) = explode( '/', $stData2 );
   if ("$ano1$mes1$dia1" > "$ano2$mes2$dia2" )return true;
   else return false;
}
/**************************************************************************/
/**** pega o a mascara reduzida do cod estrutural                       ***/
/**** Se o código for 1.4.2.1.1.02.07.00.00.00 retorna 1.4.2.1.1.02.07. ***/
/**** Autor: Fernando Zank Correa Evangelista - 23/12/2005              ***/
/**************************************************************************/

function mascaraReduzida($codEstrutural)
{
    $arCodEstrutural = explode (".",$codEstrutural);

    $inCount = count($arCodEstrutural);
    while ($inCount > 0) {
        if ($arCodEstrutural[$inCount-1] > 0) {
            $stMascaraReduzida = $arCodEstrutural[$inCount-1].".".$stMascaraReduzida;
        }
        $inCount--;
    }

    return $stMascaraReduzida;
}

function setAjuda($UC)
{
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Ajuda.class.php';

  $stCaso = substr( $UC,3 );
  $arCasoUso = explode(".",$stCaso);
  $obAjuda = new Ajuda;
  $obAjuda->setCodGestao($arCasoUso[0]);
  $obAjuda->setCodModulo( Sessao::read('modulo'));
  $obAjuda->setCasoUso($UC);
  $obAjuda->montaHTML();
  $obAjuda->show();

}

/**************************************************************************/
/**** Converte um número inteiro para decimal                           ***/
/**** Se numero 1234 , casas 3 retorna 1.234                            ***/
/**** Autor: Vandré Miguel Ramos  - 18/10/2006                          ***/
/**************************************************************************/
function intToDecimal($valor,$casas)
{
    if ($casas > 0) {
       $casas = $casas * -1;
    }
    $valor = $valor * pow(10,$casas);

    return $valor;
}

?>
