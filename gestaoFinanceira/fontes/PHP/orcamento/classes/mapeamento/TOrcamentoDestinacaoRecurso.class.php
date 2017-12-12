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
    * Classe de mapeamento da tabela orcamento.destinacao_recurso
    * Data de Criação: 29/10/2007

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Anderson cAko Konze

    $Id: TOrcamentoDestinacaoRecurso.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoDestinacaoRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoDestinacaoRecurso()
{
    parent::Persistente();
    $this->setTabela("orcamento.destinacao_recurso");

    $this->setCampoCod('cod_destinacao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio'     ,'char'    ,true  ,'4'  ,true,false);
    $this->AddCampo('cod_destinacao','sequence',true  ,''   ,true,false);
    $this->AddCampo('descricao'     ,'varchar' ,true  ,'100',false,false);

}
}
?>
