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

/**
    * Classe que contêm uma estrutura de armazenamento para dados relacionados a erro
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
include_once 'Erro.class.php';

class Excecao extends Erro
{
var $stLocal;//Retorna o Frame onde esta sendo executada a ação
var $boAtivo;//Retorna se o sistema deve parar ao verificar que existe um erro

function setLocal($stValor) { $this->stLocal = $stValor; }
function setAtivo($stValor) { $this->boAtivo = $stValor; }

function getLocal() { return $this->stLocal; }
function getAtivo() { return $this->boAtivo; }

function Excecao()
{
    parent::Erro();
}

function tratarErro()
{
    global $stAcao;
    global $pgProx;
    if ($stAcao) {
        $stAcao  = "n_".$stAcao;
    }
    switch ( strtolower( $this->getLocal() ) ) {
        case 'tp':
        case 'telaprincipal':
            sistemaLegado::alertaAviso($pgProx,urlencode($this->getDescricao()),$stAcao,"erro", Sessao::getId(), "../");
        break;
        case 'oc':
        case 'oculto':
        break;
        case 'popup':
        break;
        case 'iframe':
        break;
        default://TRATA NA MAIORIA DAS VZS A PR
            SistemaLegado::exibeAviso(urlencode($this->getDescricao()),$stAcao,"erro");
        break;
    }

    Sessao::setTrataExcecao( false );
    $this->setDescricao('');
    exit();
}
}
