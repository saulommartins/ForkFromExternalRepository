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
  * Classe de mapeamento da tabela ECONOMICO.ELEMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMElemento.class.php 59612 2014-09-02 12:00:51Z gelson $
* Casos de uso: uc-05.02.05
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ELEMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMElemento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMElemento()
{
    parent::Persistente();
    $this->setTabela('economico.elemento');

    $this->setCampoCod('cod_elemento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_elemento','integer',true,'',true,false);
    $this->AddCampo('nom_elemento','varchar',true,'80',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                           \n";
    $stSql .= "    E.*                                          \n";
    $stSql .= "FROM                                             \n";
    $stSql .= "    economico.elemento E                         \n";
    $stSql .= "    LEFT JOIN economico.baixa_elemento BE ON     \n";
    $stSql .= "          E.cod_elemento = BE.cod_elemento       \n";
    $stSql .= "WHERE                                            \n";
    $stSql .= "    BE.cod_elemento IS NULL                      \n";

    return $stSql;
}

}
