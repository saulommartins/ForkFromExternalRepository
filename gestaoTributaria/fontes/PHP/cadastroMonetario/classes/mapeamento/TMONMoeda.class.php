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
    * Classe de regra de negócio para MONETARIO.MOEDA
    * Data de Criação: 16/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONMoeda.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.06
*/

/*
$Log$
Revision 1.4  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONMoeda extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONMoeda()
{
    parent::Persistente();
    $this->setTabela("monetario.moeda");

    $this->setCampoCod('cod_moeda');
    $this->setComplementoChave('');

    $this->AddCampo('cod_moeda','integer',true,'',true,false);
    $this->AddCampo('descricao_singular','varchar',true,'40',false,false);
    $this->AddCampo('descricao_plural','varchar',true,'40',false,false);
    $this->AddCampo('fracao_singular','varchar',true,'40',false,false);
    $this->AddCampo('fracao_plural','varchar',true,'40',false,false);
    $this->AddCampo('simbolo','varchar',true,'4',false,false);
    $this->AddCampo('inicio_vigencia','date',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT                                    \n";
    $stSql = "  mo.cod_moeda,                           \n";
    $stSql = "  mo.descricao_singular,                  \n";
    $stSql = "  mo.descricao_plural,                    \n";
    $stSql = "  mo.fracao_singular,                     \n";
    $stSql = "  mo.fracao_plural,                       \n";
    $stSql = "  mo.simbolo,                             \n";
    $stSql = "  mo.inicio_vigencia,                     \n";
    $stSql = "  rm.cod_funcao,                          \n";
    $stSql = "  rm.cod_biblioteca,                      \n";
    $stSql = "  rm.cod_modulo                           \n";
    $stSql = "FROM                                      \n";
    $stSql = "  monetario.moeda as mo                   \n";
    $stSql = "INNER JOIN                                \n";
    $stSql = "  monetario.regra_conversao_moeda as rm   \n";
    $stSql = "ON                                        \n";
    $stSql = "  mo.cod_moeda = rm.cod_moeda             \n";

return $stSql;
}

}
