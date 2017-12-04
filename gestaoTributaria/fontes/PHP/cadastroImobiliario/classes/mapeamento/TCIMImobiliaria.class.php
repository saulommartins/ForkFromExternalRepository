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
     * Classe de mapeamento para a tabela IMOBILIARIO.IMOBILIARIA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMImobiliaria.class.php 59612 2014-09-02 12:00:51Z gelson $

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
  * Efetua conexão com a tabela  IMOBILIARIO.IMOBILIARIA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMImobiliaria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMImobiliaria()
{
    parent::Persistente();
    $this->setTabela('imobiliario.imobiliaria');

    $this->setCampoCod('creci');
    $this->setComplementoChave('');

    $this->AddCampo('creci'      ,'varchar',true,'10',true ,true);
    $this->AddCampo('responsavel','varchar',true,'10',false,true);
    $this->AddCampo('numcgm'     ,'integer',true,''  ,false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                             \n";
    $stSql .= "    IMO.CRECI,                     \n";
    $stSql .= "    IMO.NUMCGM,                    \n";
    $stSql .= "    CGM.NOM_CGM,                   \n";
    $stSql .= "    IMO.RESPONSAVEL,               \n";
    $stSql .= "    COR.NUMCGM  AS CGM_RESP,       \n";
    $stSql .= "    CGMR.NOM_CGM AS NOME_RESP      \n";
    $stSql .= "FROM                               \n";
    $stSql .= "  imobiliario.imobiliaria AS IMO   \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  sw_cgm AS CGM                    \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    IMO.NUMCGM  = CGM.NUMCGM       \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  imobiliario.corretor AS COR      \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    IMO.RESPONSAVEL = COR.CRECI    \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  sw_cgm AS CGMR                   \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    COR.NUMCGM  = CGMR.NUMCGM      \n";

    return $stSql;
}

}
