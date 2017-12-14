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
 * Arquivo com Exceção Genérica para LDO
 * Data de Criação: 17/02/09
 *
 * @author Bruno Ferreira <bruno.ferreira>

 $Id$

 */

/**
 * Classe de Exceção Genérica para LDO
 * @author Bruno Ferreira <bruno.ferreira>
 */
class LDOExcecao extends Exception
{
    private $stAutor     = null;
    private $stPacote    = null;
    private $stSubpacote = null;
    private $stUC        = null;
    private $inCodigo    = 0;
    private $inLinha     = 0;
    private $stMensagem  = null;

    public function __construct($stMensagem, $arAnotacoes)
    {
        $arErro = error_get_last();
        $this->stAutor = $arAnotacoes['autor'];
        $this->stUsuario = $arAnotacoes['usuario'];
        $this->stPacote = $arAnotacoes['pacote'];
        $this->stSubpacote = $arAnotacoes['subpacote'];
        $this->stUC = $arAnotacoes['uc'];
        $this->inCodigo = $arErro['type'];
        $this->stMensagem = $arErro['message'];
        $this->inLinha = $arErro['line'];

        parent::__construct($stMensagem, $this->inCodigo);
    }

    protected function recuperarMensagem($stCamada)
    {
        $obX9 = new X9();
        $arStackTrace = $this->getTrace();

        # Retorna o último elemento do stack trace.
        $arFrame = array_pop($arStackTrace);
        $stClasse = $arFrame['class'];
        $stFuncao = $arFrame['function'];

        $stMensagem  = '<fieldset><legend><b>ERRO na camada de ';
        $stMensagem .= $stCamada . ': ';
        $stMensagem .= $this->getMessage() . ' na classe ' . $stClasse;
        $stMensagem .= '</b></legend><pre><br />';
        $stMensagem .= '<b>O erro ocorreu no arquivo:</b> ';
        $stMensagem .= $this->getFile() . " na linha " . $this->getLine();
        $stMensagem .= '<br /><b>Ao tentar executar a metodo:</b> ';
        $stMensagem .= $funcao . '<br /><b>Codigo:</b> ';
        $stMensagem .= $this->getTraceAsString();
        $stMensagem .= '<br /><b>ERRO PHP:</b> Codigo: ';
        $stMensagem .= $this->inCodigo . " - " . $this->stMensagem;
        $stMensagem .= ' - ' . $this->inLinha . '</pre></fieldset>';

        $obX9->setAutor($this->stAutor);
        $obX9->setUsuario($this->stUsuario);
        $obX9->setPacote($this->stPacote);
        $obX9->setSubpacote($this->stSubpacote);
        $obX9->setUC($this->stUC);
        $obX9->setDescricao($stMensagem);
        $obX9->setTitulo($this->stUC . ' :: ' . $this->getMessage());
        $obX9->setErro($this->mensagem);

        X9Erro::executar($obX9);

        return $mensagem;
    }
}

/* Causa muitos warnings no framework */
//error_reporting(E_ALL);

?>
