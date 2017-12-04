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
* Classe para compressao de arquivos
* Data de Criação: 10/02/2005

* @author Analista/Desenvolvedor: Diego Barbosa Victoria

* @package Framework
* @subpackage Arquivos

$Revision: 22293 $
$Name$
$Author: andre $
$Date: 2007-05-02 17:49:41 -0300 (Qua, 02 Mai 2007) $

Casos de uso: uc-01.01.00

*/

/**
* Classe para compressao de arquivos
* @author Analista/Desenvolvedor: Diego Barbosa Victoria
* @package Framework
* @subpackage Arquivos
*/
include_once ( CLA_ZIP_FILE );
class ArquivoZip extends zipfile
{
    /**
    * Ponteiro do Arquivo Temporario
    *
    */
    public $obArquivoTmp   = '';

    /**
    * Nome do Arquivo a ser processado
    *
    */
    public $stNomeArquivo  = '';

    /**
    * Nome do Arquivo temporario
    *
    */
    public $stNomeArquivoTmp  = '';

    //SETANDO
    public function setArquivoTmp($valor) { $this->stArquivoTmp       = $valor    ;   }
    public function setNomeArquivo($valor) { $this->stNomeArquivo      = $valor    ;   }
    public function setNomeArquivoTmp($valor) { $this->stNomeArquivoTmp   = $valor    ;   }
    //GETANDO
    public function getArquivoTmp() { return $this->stArquivoTmp            ;   }
    public function getNomeArquivo() { return $this->stNomeArquivo           ;   }
    public function getNomeArquivoTmp() { return $this->stNomeArquivoTmp        ;   }

    /**
    * Função Construtura
    */
    public function ArquivoZip()
    {
    }

    /**
    * Adiciona Arquivo
    * @return void()
    * @desc Adiciona arquivo na compressao corrente.
    */
    public function AdicionarArquivo($obArquivo,$stNome,$stDir='',$inTime=0,$adicionaCaminho=true)
    {
        // Se stDir estiver setado
        if (!$stDir=='') {
            // Se nao tiver / no fim da string, coloca
            if (!substr($stDir,-1,1)=='/' ) {$stDir = $stDir.'/';}
        }

        //teste de zipar arquivo com caminho completo ou não -> não estava funcionando zipar arquivos comuns
        if ($adicionaCaminho == true) {
            $stNomeDisco = $stDir.$stNome;
        } else {
            $stNomeDisco = $stNome;
        }

        // Le arquivo passado
        $abre       = fopen($obArquivo, "r");
        // error_reporting(E_ERROR);

        $filesize = filesize($obArquivo);

        /* se tamanho do arquivo for 0 ou menor, o arquivo esta vazio, para nao dar pau no zip, cria-se um arquivo com 1 byte */
        if ($filesize > 0) {
            $obArquivo = fread($abre, filesize($obArquivo)); //string contendo o arquivo a ser compactado
        } else {
            $obArquivo = '';
        }

        fclose($abre);
        // error_reporting(E_ALL | E_NOTICE);
        // Adiciona no arquivo zip
        parent::addFile($obArquivo,$stNomeDisco,$inTime);
    }

    public function FinalizaZip()
    {
        $stArquivo = parent::file();

        if ($this->getNomeArquivoTmp() == "") {
            $this->setNomeArquivoTmp(sistemalegado::getmicrotime()."_exportacao.zip");
        }
        //$stArq= CAM_ANEXOS."exportador/".$this->getNomeArquivoTmp();
        $stArq= CAM_FRAMEWORK."tmp/".$this->getNomeArquivoTmp();
        // seta o nome ficticio para download, caso o usuario set , sera sobreposto depois
        $this->setNomeArquivo($this->getNomeArquivoTmp());
        $abre = fopen($stArq, "w");
        $salva = fwrite($abre, $stArquivo);
        fclose($abre);
        $this->setArquivoTmp($stArq);

        return 0;//$stArq;
    }

    /**
    * @return void()
    * @desc Finaliza Automagicamente o arquivo zip corrente
    * e força o download.
    */
    public function Show()
    {
        // Grava Finalizador do Arquivo
        $this->FinalizaZip();
        $len = filesize($this->getArquivoTmp());
        $filename = basename($this->getArquivoTmp());
        if (!headers_sent()) {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Type: application/zip");
            //Força o download
            $header="Content-Disposition: attachment; filename=".$this->getNomeArquivo().";";
            header($header );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$len);
            @readfile($this->getArquivoTmp());
        }

        return $this->getNomeArquivo();
    }
}
?>
