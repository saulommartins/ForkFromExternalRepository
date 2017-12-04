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
* Gerar o componente de ajuda
* Data de Criação: 21/11/2005

* @author Desenvolvedor: Lucas Stephanou

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gera o componente tipo applet de acordo com os valores setados pelo Usuário
    * @author Analista: Lucas Leusin
    * @author Documentor: Anderson R. M. Buzo

    * @package Interface
    * @subpackage Componente
*/
class Ajuda extends Componente
{
/**
    * @access Private
    * @var Integer
*/
var $inWidth;
/**
    * @access Private
    * @var String
*/
var $stDiretorio;
/**
    * @access Private
    * @var Integer
*/
var $inCodGestao;
/**
    * @access Private
    * @var Integer
*/
var $inCodModulo;
/**
    * @access Private
    * @var String
*/
var $stCasoUso;

/***** funções para setar parametros******/
/**
    * @access Public
    * @param Integer $valor
*/
function setWidth($valor) { $this->inWidth     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGestao($valor) { $this->inCodGestao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodModulo($valor) { $this->inCodModulo = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCasoUso($valor) { $this->stCasoUso = $valor; }

/***** funções para pegar parametros******/
/**
    * @access Private
    * @return Integer
*/
function getWidth() { return $this->inWidth;      }
/**
    * @access Private
    * @return Integer
*/
function getCodGestao() { return $this->inCodGestao;      }
/**
    * @access Private
    * @return Integer
*/
function getCodModulo() { return $this->inCodModulo;      }
/**
    * @access Private
    * @return Integer
*/
function getCasoUso() { return $this->stCasoUso;      }

/*********************/
/**
    * Método Construtor
    * @access Public
*/
function Ajuda()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setWidth         ( 400        );
    $this->setName          ( "obAjuda"  );
    $this->setDefinicao     ( "ajuda"    );
}

function getDiretorioGestao(&$stDiretorioGestao)
{
    $stSql = "SELECT nom_diretorio FROM administracao.gestao WHERE cod_gestao = ".$this->getCodGestao()."";
    $obConexao = new Conexao;
    $obErro = new Erro;
    $obErro = $obConexao->executaSQL($rsDirGestao,$stSql,'');
    if (!$obErro->ocorreu() ) {
        $stDiretorioGestao = $rsDirGestao->getCampo("nom_diretorio")."../Manuais/HTML";
    }

    return $obErro;
}

function getDiretorioModulo(&$stDiretorioModulo)
{
    $stSql = "SELECT nom_diretorio FROM administracao.modulo WHERE cod_modulo = ".$this->getCodModulo()."";
    $obConexao = new Conexao;
    $obErro = new Erro;
    $obErro = $obConexao->executaSQL($rsDirModulo,$stSql,'');
    if (!$obErro->ocorreu() ) {
        $stDiretorioModulo = $rsDirModulo->getCampo("nom_diretorio")."";
    }

    return $obErro;
}

function getDiretorio(&$stDiretorio)
{
    $this->getDiretorioGestao($stDirGestao);
    $this->getDiretorioModulo($stDirModulo);
    //$stDiretorio = $stDirGestao.$stDirModulo.$this->getCasoUso()."/man".$this->getCasoUso().".html";
    $stDiretorio = "../../../../../Manuais/HTML/".$stDirModulo.strtoupper($this->getCasoUso())."/man".strtoupper($this->getCasoUso()).".html";
}

/**
    * Monta o HTML do Objeto TextBox
    * @access Protected
*/
function montaHtml()
{
    $stHtml = "";
    $this->getDiretorio($stDiretorio);
    if ( is_file($stDiretorio)) {
       // echo "<!-- div id="sample1" class="window" style="left:-3000px;top:30px;width:600px;"-->";
        $stHtml .= '
        <!-- INICIO AJUDA -->
        <a id="link_ajuda" href="" onclick="if (winList[\'sample1\']) winList[\'sample1\'].open(); return false;" alt="Ajuda">
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
            <iframe src="'.$stDiretorio.'" height="98%" width="99%"></iframe>
            </div>
        </div>
        <!-- FIM AJUDA-->
        ';
        $stHtml = '
            <a id="link_ajuda" href=""
                onclick=\'
                    var w = screen.width * 0.80; var h = screen.height * 0.80;
                    window.open("'.$stDiretorio.'","","width=w,height=h,menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes");

                    return false;
                \' alt=\'Ajuda\'>
            <span alt="Ajuda">Ajuda</span>
            </a>
        ';

    } else {
        $stHtml = "";

    }
    $this->setHtml( $stHtml );
}

}
?>
