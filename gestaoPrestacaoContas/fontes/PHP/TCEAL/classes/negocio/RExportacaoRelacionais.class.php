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

    * Classe para exportar arquivos XML com informações da Execução
    * Data de Criação   : 17/09/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    $Id:$
    *
    * @ignore
*/

include_once(CAM_GPC_TCEAL_MAPEAMENTO."TExportacaoRelacionais.class.php");
include_once(CLA_ARQUIVO_ZIP);

class RExportacaoRelacionais
{
    private $obXML;
    private $obTExportacaoRelacionais;
    private $obArquivoZip;
    private $nomeDocumento;
    private $inBimestre;
    private $stEntidades;
    private $stVersao;

    public function __construct()
    {
        $this->obXML = new XMLWriter();
        $this->obTExportacaoRelacionais = new TExportacaoRelacionais();
        $this->obArquivoZip = new ArquivoZip();
        $this->setVersao("1.0");
    }

    public function iniciaDocumento()
    {
        # Cria memoria para armazenar a saida
        $this->obXML->openMemory();

        # Seta identação para visualizar melhor
        $this->obXML->setIndent(true);

        # Inicia o cabeçalho do documento XML
        $this->obXML->startDocument( '1.0' , 'UTF-8', 'yes' );
    }

    //seta o nome do documento xml que vai ser gerado
    private function setNomeDocumento($nomeDocumento)
    {
        $this->nomeDocumento = $nomeDocumento;
        if (!preg_match("/xml|XML/", $nomeDocumento)) {
            $this->nomeDocumento .= ".xml";
        }
    }

    //seta o bimestre ao qual os documentos vão ser gerados
    public function setBimestre($inBimestre)
    {
        $this->inBimestre = $inBimestre;
    }

     //seta o bimestre ao qual os documentos vão ser gerados
    public function setEntidades($stEntidades)
    {
        $this->stEntidades = $stEntidades;
    }
    
     //seta a versão ao qual os documentos vão ser gerados
    public function setVersao($stVersao)
    {
        $this->stVersao = $stVersao;
    }
    
    //retorna a versão ao qual os documentos vão ser gerados
    public function getVersao()
    {
        return $this->stVersao;
    }

    public function finalizaDocumento()
    {
        file_put_contents(CAM_FRAMEWORK."tmp/".$this->nomeDocumento, $this->obXML->outputMemory(true));
    }

    public function strSemAcentos($string="", $mesma=1)
    {
            if ($string != "") {
                $com_acento = "à á â ã ä è é ê ë ì í î ï ò ó ô õ ö ù ú û ü À Á Â Ã Ä È É Ê Ë Ì Í Î Ò Ó Ô Õ Ö Ù Ú Û Ü ç Ç ñ Ñ";
                $sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I O O O O O U U U U c C n N";
                $c = explode(' ',$com_acento);
                $s = explode(' ',$sem_acento);

                $i=0;
                foreach ($c as $letra) {

                    if (preg_match("/".$letra."/", $string)) {
                        $pattern[] = $letra;
                        $replacement[] = $s[$i];
                    }
                    $i=$i+1;
                }

                if (isset($pattern)) {
                    $i=0;
                    foreach ($pattern as $letra) {
                        $string = preg_replace("/".$letra."/i", $replacement[$i], $string);
                        $i=$i+1;
                    }

                    return $string; # retorna string alterada
                }
                if ($mesma != 0) {
                    return $string; # retorna a mesma string se nada mudou
                }
            }

        return ""; # sem mudança retorna nada
    }
 
    public function geraDocumentoXML($arResult, $stNomeArquivo='')
    {        
        $this->iniciaDocumento();
        
        $this->obXML->startElement("SICAP");
        $this->obXML->writeAttribute("versao", $this->getVersao());
        
        $this->setNomeDocumento($stNomeArquivo);
        
        if (count($arResult)) {
            foreach ($arResult as $result) {
                $this->obXML->startElement($stNomeArquivo);
                foreach ($result as $key=>$value) {
                    $this->obXML->startElement($key);
                    $this->obXML->text($value);
                    $this->obXML->endElement();
                }
                $this->obXML->endElement();
            }
        }
        
        $this->obXML->endElement();
        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();
        $this->finalizaDocumento();
        
        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function doZipArquivos()
    {
        $arArquivosDownload = Sessao::read('arArquivosDownload');
        $stLabelZip = 'ArquivosExportacao.zip';
        $stCaminho = CAM_FRAMEWORK.'tmp/';

        foreach ($arArquivosDownload as $arquivo) {
            $this->obArquivoZip->AdicionarArquivo($arquivo['stLink'],$arquivo['stNomeArquivo']);
        }

        $stNomeZip = $this->obArquivoZip->Show();
        $arArquivosDownload = array();
        $arArquivosDownload[0]['stNomeArquivo'] = $stLabelZip;
        $arArquivosDownload[0]['stLink'       ] = $stCaminho.$stNomeZip;
        // Manda array de arquivos para a sessao
        Sessao::write('arArquivosDownload',$arArquivosDownload);
    }
}
