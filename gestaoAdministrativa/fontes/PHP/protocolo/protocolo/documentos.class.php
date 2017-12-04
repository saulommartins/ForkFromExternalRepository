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
* Classe de negócio Documentos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.96
*/

class documentos
{
/**************************************************************************/
/**** Declaração das variáveis                                          ***/
/**************************************************************************/
    public $codDocumento;
    public $nomDocumento;
    public $documento;
    public $tipoProcesso;
    public $comboDocumento;
    public $codProcesso;
    public $documentoDigital;
    public $anoE;
    public $diretorio;
    public $tipoDocumentoDigital;
    public $codDocumentoDigital;

/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/
    public function documentos()
    {
        $this->codDocumento = "";
        $this->nomDocumento = "";
        $this->documento = "";
        $this->tipoProcesso = "";
        $this->comboDocumento = "";
        $this->codProcesso = "";
        $this->documentoDigital = "";
        $this->anoE = "";
        $this->diretorio = "";
        $this->tipoDocumentoDigital = "";
        $this->codDocumentoDigital = "";
        }

/**************************************************************************/
/**** Método que faz a inserção dos documentos                          ***/
/**************************************************************************/
    public function insereDocumento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO sw_documento (cod_documento, nom_documento) VALUES ('".$this->codDocumento."', '".$this->nomDocumento."')";
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveis($codDocumento, $nomDocumento="")
    {
        $this->codDocumento = $codDocumento;
        $this->nomDocumento = $nomDocumento;
        }

/**************************************************************************/
/**** Método que faz o Update dos documentos                            ***/
/**************************************************************************/
    public function updateDocumento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_documento SET nom_documento = '".$this->nomDocumento."' WHERE cod_documento = '".$this->codDocumento."'";
        if ($dbConfig->executaSql($insert))
            return true;
        else
        return false;
        $dbConfig->fechaBd();
    }

/**************************************************************************/
/**** Método que faz o Delete dos Documentos                            ***/
/**************************************************************************/
    public function deleteDocumento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM sw_documento WHERE cod_documento='".$this->codDocumento."'";
        if ($dbConfig->executaSql($insert))
            return true;
        else
        return false;
        $dbConfig->fechaBd();
    }

/**************************************************************************/
/**** Método que faz o Combo dos tipos de Documentos                    ***/
/**************************************************************************/
function listaComboDocumentos()
{
        $sSQL = "SELECT cod_documento, nom_documento FROM sw_documento ORDER by nom_documento";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboDocumento = "";
        $this->comboDocumento .= "<select name=codDocumento>\n<option value=xxx SELECTED>Selecione</option>";
        while (!$dbEmp->eof()) {
            $codDoc  = trim($dbEmp->pegaCampo("cod_documento"));
            $nomDoc  = trim($dbEmp->pegaCampo("nom_documento"));
            $dbEmp->vaiProximo();
            $this->comboDocumento .= "<option value=".$codDoc;
            $this->comboDocumento .=">".$nomDoc."</option>\n";
    }
        $this->comboDocumento .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo de documentos gerado                     ***/
/**************************************************************************/
    public function mostraComboDocumentos()
    {
        echo $this->comboDocumento;
    }
/**************************************************************************/
/**** Método que pega as variaveis para tratamento de Doc. Digital      ***/
/**************************************************************************/
    public function setaVariaveisDocDigital($codDocumento, $codProcesso, $anoE, $documentoDigital, $tipoDocumentoDigital="", $diretorio="",$codDocumentoDigital="")
    {
        $this->codDocumento = $codDocumento;
        $this->codProcesso = $codProcesso;
        $this->anoE = $anoE;
        $this->documentoDigital = $documentoDigital;
        $this->tipoDocumentoDigital = $tipoDocumentoDigital;
        $this->diretorio = $diretorio;
        $this->codDocumentoDigital = $codDocumentoDigital;
        }
/**************************************************************************/
/**** Método que faz o Update dos documentos digitais                   ***/
/**************************************************************************/
    public function updateDocumentoDigital()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_documento_processo SET anexo = '".$this->documentoDigital."', tipo_anexo = ".$this->tipoDocumentoDigital." WHERE cod_documento = ".$this->codDocumento." AND cod_processo = ".$this->codProcesso." AND ano_exercicio = ".$this->anoE." AND cod_documento_processo = ".$this->codDocumentoDigital;
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
        return false;
        $dbConfig->fechaBd();
    }
/**************************************************************************/
/**** Método que faz o Exclude dos documentos digitais                  ***/
/**************************************************************************/
    public function deleteDocumentoDigital()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_documento_processo SET anexo = '', tipo_anexo = '' WHERE cod_documento = ".$this->codDocumento." AND cod_processo = ".$this->codProcesso." AND ano_exercicio = ".$this->anoE." AND cod_documento_processo = ".$this->codDocumentoDigital;
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
        return false;
        $dbConfig->fechaBd();
    }
/**************************************************************************/
/**** Método que faz o Exclude dos documentos digitais                  ***/
/**************************************************************************/
function apagaDocumentoDigital()
{
    $chaves = 0;
    $nomeArq = $this->codProcesso."_".$this->anoE."_".$this->codDocumento."_".$this->codDocumentoDigital.".jpg";
    $nomeArqTh = "th_".$this->codProcesso."_".$this->anoE."_".$this->codDocumento."_".$this->codDocumentoDigital.".jpg";
    $CompArq = $this->diretorio."/anexos/".$nomeArq;
    $CompArqTh = $this->diretorio."/anexos/".$nomeArqTh;
    //echo $CompArq;
    if (exec("rm ".$CompArq))
    $chaves++;
    if (exec("rm ".$CompArqTh))
    $chaves++;
    if ($chaves == 0)
        return false;
    else
        return true;

}//Fim método
/**************************************************************************/
/**** Método que faz o Exclude dos documentos digitais que nao imagens  ***/
/**************************************************************************/
function apagaDocumentoDigitalS()
{
    $chaves = 0;
    $nomeArq = $this->documentoDigital;
    $CompArq = $this->diretorio."/anexos/".$nomeArq;
    //echo $CompArq;
    if (exec("rm ".$CompArq))
    $chaves++;
    if ($chaves == 0)
        return false;
    else
        return true;

}//Fim método

}//Fim Classe
