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
    * Classe de mapeamento da tabela ORCAMENTO.PAO
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: jose.eduardo $
    $Date: 2006-08-17 15:47:41 -0300 (Qui, 17 Ago 2006) $

    * Casos de uso: uc-02.01.03 , uc-02.08.02
*/

/*
$Log$
Revision 1.1  2006/08/17 18:43:05  jose.eduardo
Bug #6739#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.FONTE_RECURSO
  * Data de Criação: 17/08/2006

  * @author Analista: Cleisson Barboza
  * @author Desenvolvedor: José Eduardo Porto

*/
class TOrcamentoFonteRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoFonteRecurso()
{
    parent::Persistente();
    $this->setTabela('orcamento.fonte_recurso');

    $this->setCampoCod('cod_fonte');

    $this->AddCampo('cod_fonte', 'integer', true, ''  , true , false);
    $this->AddCampo('descricao', 'varchar', true, '40', false, false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "  SELECT                                   \n";
    $stSql .= "     cod_fonte,                            \n";
    $stSql .= "     descricao                             \n";
    $stSql .= "  FROM                                     \n";
    $stSql .= "     orcamento.fonte_recurso               \n";

    return $stSql;
}
}
