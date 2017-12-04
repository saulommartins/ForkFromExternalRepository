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
    * Classe de mapeamento da tabela TESOURARIA_TERMINAL
    * Data de Criação: 06/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.16  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TERMINAL
  * Data de Criação: 06/09/2005

  * @author Analista: Lucas Oiagen
  * @author Desenvolvedor: Cleisson da Silva Barboza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTerminal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTerminal()
{
    parent::Persistente();
    $this->setTabela("tesouraria.terminal");

    $this->setCampoCod('cod_terminal');
    $this->setComplementoChave('timestamp_terminal');

    $this->AddCampo('cod_terminal'      ,'integer'  ,true,''  , true , false );
    $this->AddCampo('timestamp_terminal','timestamp',true,''  , true , false );
    $this->AddCampo('cod_verificador'   ,'varchar'  ,true,'40', false, false );
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                              \n";
    $stSql .= "     tbl.*,                                                          \n";
    $stSql .= "     CASE                                                            \n";
    $stSql .= "         WHEN tbl.timestamp_desativado is null THEN 'Ativo'          \n";
    $stSql .= "         WHEN tbl.timestamp_desativado is not null THEN 'Inativo'    \n";
    $stSql .= "     END as situacao                                                 \n";
    $stSql .= "                                                                     \n";
    $stSql .= " FROM(                                                               \n";
    $stSql .= "     SELECT                                                          \n";
    $stSql .= "         TT.timestamp_terminal,                                      \n";
    $stSql .= "         TT.cod_terminal,                                            \n";
    $stSql .= "         TT.cod_verificador,                                         \n";
    $stSql .= "         UT.cgm_usuario,                                             \n";
    $stSql .= "         UT.timestamp_usuario,                                       \n";
    $stSql .= "         CGM.nom_cgm,                                                \n";
    $stSql .= "         TD.timestamp_desativado,                                    \n";
    $stSql .= "         UTE.timestamp_excluido                                      \n";
    $stSql .= "     FROM                                                            \n";
    $stSql .= "         tesouraria.terminal as TT                                   \n";
    $stSql .= "     LEFT JOIN tesouraria.terminal_desativado     as TD on(          \n";
    $stSql .= "         TT.cod_terminal         = TD.cod_terminal                   \n";
    $stSql .= "     AND TT.timestamp_terminal   = TD.timestamp_terminal             \n";
    $stSql .= "     ),                                                              \n";
    $stSql .= "     tesouraria.usuario_terminal as UT                               \n";
    $stSql .= "     LEFT JOIN tesouraria.usuario_terminal_excluido as UTE on(       \n";
    $stSql .= "         UT.timestamp_usuario    = UTE.timestamp_usuario             \n";
    $stSql .= "     AND UT.timestamp_terminal   = UTE.timestamp_terminal            \n";
    $stSql .= "     AND UT.cod_terminal         = UTE.cod_terminal                  \n";
    $stSql .= "     AND UT.cgm_usuario          = UTE.cgm_usuario                   \n";
    $stSql .= "     ),                                                              \n";
    $stSql .= "     sw_cgm                      as CGM                              \n";
    $stSql .= "     WHERE                                                           \n";
    $stSql .= "         UT.cgm_usuario          = CGM.numcgm                        \n";
    $stSql .= "     AND TT.timestamp_terminal   = UT.timestamp_terminal             \n";
    $stSql .= "     AND TT.cod_terminal         = UT.cod_terminal                   \n";
    $stSql .= "     AND UT.responsavel          = true                              \n";
    $stSql .= "     AND UTE.timestamp_excluido is null                              \n";
    $stSql .= " ) as tbl                                                            \n";

    return $stSql;
}

}
