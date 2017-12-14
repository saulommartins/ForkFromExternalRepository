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
* Classe de Mapeamento para a tabela assunto
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: TPROAssunto.class.php 59612 2014-09-02 12:00:51Z gelson $

$Revision: 16316 $
$Name$
$Author: cassiano $
$Date: 2006-10-03 13:10:47 -0300 (Ter, 03 Out 2006) $

Casos de uso: uc-01.06.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once(CAM_GA_PROT_MAPEAMENTO."TAssunto.class.php");
//FAZER AS ALTERAÇÕES NA TASSUNTO
class TPROAssunto extends TAssunto
{
function TPROAssunto()
{
    parent::TAssunto();
}

function validaExclusao($stFiltro = "" , $boTransacao = "")
{
    $obErro = new Erro();
    include_once(CAM_GA_PROT_MAPEAMENTO."TProtocoloProcesso.class.php");
    $obTProtocoloProcesso = new TProtocoloProcesso();
    $stFiltro  = ' WHERE ';
    $stFiltro .= ' cod_classificacao='.$this->getDado('cod_classificacao').' AND ';
    $stFiltro .= ' cod_assunto='.$this->getDado('cod_assunto');
    $obErro = $obTProtocoloProcesso->recuperaTodos($rsProcesso,$stFiltro);
    if ( !$rsProcesso->eof() ) {
        $obErro->setDescricao('Já existem processos relacionados ao assunto código '.$this->getDado('cod_assunto').'!');
        if ( Sessao::read('boTrataExcecao') ) {
            Sessao::getExcecao()->setDescricao($obErro->getDescricao());
        }
    }

    return $obErro;
}

}
