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

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAssunto extends Persistente
{
function TAssunto()
{
    parent::Persistente();
    $this->setTabela('sw_assunto');
    $this->setCampoCod('cod_assunto');
    $this->setComplementoChave('cod_classificacao');

    $this->AddCampo('cod_assunto',		'integer',true,'',	true,false);
    $this->AddCampo('cod_classificacao','integer',true,'',	true,'TPROClassificacao');
    $this->AddCampo('nom_assunto',		'varchar',true,'',	false,false);
    $this->AddCampo('confidencial',		'boolean',true,1,	false,false);
}

function validaInclusao($stFiltro = "" , $boTransacao = "")
{
    $obErro = new Erro;

    $stFiltro  = " WHERE cod_classificacao=".$this->getDado('cod_classificacao');
    $stFiltro .= " AND UPPER(nom_assunto) ILIKE '".$this->getDado('nom_assunto_validacao')."'";
    $obErro = $this->recuperaTodos($rsAssunto,$stFiltro);
    
    if ( !$obErro->ocorreu() ) {
        if ( !$rsAssunto->eof() ) {
            $obErro->setDescricao("O assunto ".$this->getDado('nom_assunto')." já existe para esta classificação!");
            if ( Sessao::read('boTrataExcecao') ) {
                Sessao::getExcecao()->setDescricao($obErro->getDescricao());
            }
        }
    }

    return $obErro;
}

}
