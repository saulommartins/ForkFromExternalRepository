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
     * Classe de mapeamento para a tabela IMOBILIARIO.CORRETOR
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMCorretor.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CORRETOR
  * Data de Criação: 18/01/2005

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMCorretor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMCorretor()
{
    parent::Persistente();
    $this->setTabela('imobiliario.corretor');

    $this->setCampoCod('creci');
    $this->setComplementoChave('');

    $this->AddCampo('creci' ,'varchar',true,'10',true ,true);
    $this->AddCampo('numcgm','integer',true,''  ,false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                             \n";
    $stSql .= "    COR.CRECI,                     \n";
    $stSql .= "    COR.NUMCGM,                    \n";
    $stSql .= "    CGM.NOM_CGM                    \n";
    $stSql .= "FROM                               \n";
    $stSql .= "  imobiliario.corretor AS COR      \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  sw_cgm AS CGM                    \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    COR.NUMCGM = CGM.NUMCGM        \n";

    return $stSql;
}

}
