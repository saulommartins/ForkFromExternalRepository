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
* Classe de negócio Modelo Documento
* Data de Criação: 22/02/2006

* @author Analista:
* @author Desenvolvedor: Lucas Teixeira Stephanou

$Revision: 26898 $
$Name$
$Author: rodrigosoares $
$Date: 2007-11-26 09:42:12 -0200 (Seg, 26 Nov 2007) $

Casos de uso: uc-01.03.100
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RAdministracaoAcao.class.php");
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php");
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoArquivosDocumento.class.php");
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloArquivosDocumento.class.php");

class RModeloDocumento
{
    public $inCodDocumento;
    public $stNomeDocumento;
    public $stNomeArquivoAgt;
    public $inCodArquivo;
    public $stNomeArquivoOO;
    public $stCheckSum;
    public $boSistema;
    public $boPadrao;
    public $obRAdministracaoAcao;
    public $obTAdministracaoModeloDocumento;
    public $obTAdministracaoArquivosDocumento;
    public $obTAdministracaoModeloArquivosDocumento;
    public $arArquivosIncluidos;
    public $arArquivosExcluidos;

    public function setCodDocumento($valor) { $this->inCodDocumento        = $valor; }
    public function setNomeDocumento($valor) { $this->stNomeDocumento       = $valor; }
    public function setNomeArquivoAgt($valor) { $this->stNomeArquivoAgt      = $valor; }
    public function setCodArquivo($valor) { $this->inCodArquivo          = $valor; }
    public function setNomeArquivoOO($valor) { $this->stNomeArquivoOO       = $valor; }
    public function setCheckSum($valor) { $this->stCheckSum            = $valor; }
    public function setSistema($valor) { $this->stSistema             = $valor; }
    public function setPadrao($valor) { $this->stPadrao              = $valor; }
    public function setArquivosIncluidos($valor) { $this->arArquivosIncluidos   = $valor; }
    public function setArquivosExcluidos($valor) { $this->arArquivosExcluidos   = $valor; }

    public function getCodDocumento() { return $this->inCodDocumento     ; }
    public function getNomeDocumento() { return $this->stNomeDocumento    ; }
    public function getNomeArquivoAgt() { return $this->stNomeArquivoAgt   ; }
    public function getCodArquivo() { return $this->inCodArquivo       ; }
    public function getNomeArquivoOO() { return $this->stNomeArquivoOO    ; }
    public function getCheckSum() { return $this->stCheckSum         ; }
    public function getSistema() { return $this->stSistema          ; }
    public function getPadrao() { return $this->stPadrao           ; }
    public function getArquivosIncluidos() { return $this->arArquivosIncluidos; }
    public function getArquivosExcluidos() { return $this->arArquivosExcluidos; }

function RModeloDocumento()
{
    $this->obRAdministracaoAcao = new RAdministracaoAcao;
    $this->obTAdministracaoModeloDocumento = new TAdministracaoModeloDocumento;
    $this->obTAdministracaoArquivosDocumento =new TAdministracaoArquivosDocumento;
    $this->obTAdministracaoModeloArquivosDocumento = new TAdministracaoModeloArquivosDocumento;
    $this->obTransacao = new Transacao;
}
function salvaDocumento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // excluir arquivos
        foreach ( $this->getArquivosExcluidos() as $arArquivoExcluido ) {
            $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_acao"      , $arArquivoExcluido["cod_acao"]       );
            $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_documento" , $arArquivoExcluido["cod_documento"]  );
            $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_arquivo"   , $arArquivoExcluido["cod_arquivo"]    );
            $obErro = $this->obTAdministracaoModeloArquivosDocumento->exclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTAdministracaoArquivosDocumento->setDado ("cod_arquivo" , $arArquivoExcluido["cod_arquivo"]);
                $obErro = $this->obTAdministracaoArquivosDocumento->exclusao( $boTransacao );
                if ( $obErro->ocorreu() )
                    break;
            }
        }
        if ( !$obErro->ocorreu() ) {
            // incluir arquivos
            foreach ( $this->getArquivosIncluidos() as $arArquivoIncluido ) {
                $obErro = $this->obTAdministracaoArquivosDocumento->proximoCod( $inCodArquivo , $boTransacao );
                          $this->obTAdministracaoArquivosDocumento->debug();
                if ( !$obErro->ocorreu() ) {
                    $this->obTAdministracaoArquivosDocumento->setDado ("cod_arquivo"        , $inCodArquivo                     );
                    $this->obTAdministracaoArquivosDocumento->setDado ("nome_arquivo_template"   , $arArquivoIncluido["name"]        );
                    $this->obTAdministracaoArquivosDocumento->setDado ("checksum"           , $arArquivoIncluido["checksum"]    );
                    $obErro = $this->obTAdministracaoArquivosDocumento->inclusao( $boTransacao );
                              $this->obTAdministracaoArquivosDocumento->debug();
                    if ( !$obErro->ocorreu() ) {
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_acao"      , $this->obRAdministracaoAcao->getCodigoAcao()      );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_documento" , $this->getCodDocumento()         );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_arquivo"   , $inCodArquivo                    );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("sistema"       , $arArquivoIncluido["sistema"]    );
                        $obErro = $this->obTAdministracaoModeloArquivosDocumento->inclusao ( $boTransacao );
                        if ( $obErro->ocorreu() )
                            break;
                        if ( !$obErro->ocorreu() ) {
                            // se incluir, copiamos o arquivo para o local correto
                            // local para salvar
                            $stCaminhoReal = realpath(CAM_GA_ADM_ANEXOS)."/modelos_usuario/";
                            $stArquivoTmp = ini_get("session.save_path")."/".$arArquivoIncluido["name"];
                            $boCopiado = rename($stArquivoTmp,$stCaminhoReal.$this->getCodDocumento()."___".$arArquivoIncluido["name"]);
                            if ( !$boCopiado )
                                $obErro->setDescricao("Erro ao consistir arquivo.");
                        }
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->listarArquivosPorDocumentoAcao($rsArquivos, $boTransacao);
                $inCodArquivoPadrao = null;
                while (!$rsArquivos->eof()) {
                    if ($rsArquivos->getCampo("checksum") == $this->getCheckSum()) {
                        $inCodArquivoPadrao = $rsArquivos->getCampo("cod_arquivo");
                        $boSistema          = ($rsArquivos->getCampo("sistema")=="f"?"false":"true");
                    }
                    if ($rsArquivos->getCampo("padrao") == 't') {
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_documento"    , $this->getCodDocumento()  );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_acao"         , $this->obRAdministracaoAcao->getCodigoAcao()  );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_arquivo"      , $rsArquivos->getCampo("cod_arquivo")  );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("sistema"          , ($rsArquivos->getCampo("sistema")=="f")?"false":"true" );
                        $this->obTAdministracaoModeloArquivosDocumento->setDado ("padrao"           , false );
                        $obErro = $this->obTAdministracaoModeloArquivosDocumento->alteracao( $boTransacao );
                    }
                    $rsArquivos->proximo();
                }
                $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_documento"    , $this->getCodDocumento()  );
                $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_acao"         , $this->obRAdministracaoAcao->getCodigoAcao()  );
                $this->obTAdministracaoModeloArquivosDocumento->setDado ("cod_arquivo"      , $inCodArquivoPadrao  );
                $this->obTAdministracaoModeloArquivosDocumento->setDado ("sistema"          , $boSistema );
                $this->obTAdministracaoModeloArquivosDocumento->setDado ("padrao"           , true );
                $obErro = $this->obTAdministracaoModeloArquivosDocumento->alteracao( $boTransacao );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAdministracaoModeloArquivosDocumento );

    return $obErro;
}

function listarPorAcao(&$rsRecordSet,$boTransacao = "")
{
    $stFiltro .= " WHERE a.cod_acao = '".$this->obRAdministracaoAcao->getCodigoAcao()."' ";
    $stOrder   = " GROUP BY a.padrao, a.cod_acao, b.cod_documento, b.nome_documento, b.nome_arquivo_agt, b.cod_tipo_documento";
    $stOrder  .= " ORDER BY b.nome_documento ";
    $obErro = $this->obTAdministracaoModeloDocumento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
function listarArquivosPorDocumentoAcao(&$rsRecordSet,$boTransacao = "")
{
    $stFiltro = " WHERE a.cod_acao = ".$this->obRAdministracaoAcao->getCodigoAcao()." ";
    if ( $this->getCodDocumento() ) {
        $stFiltro.= " AND b.cod_documento =".$this->getCodDocumento()." ";
    }
    $stOrder = " ORDER BY c.sistema DESC, c.nome_arquivo_template ASC";
    $obErro = $this->obTAdministracaoModeloDocumento->recuperaArquivosDocumentoAcao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

} // end of class
