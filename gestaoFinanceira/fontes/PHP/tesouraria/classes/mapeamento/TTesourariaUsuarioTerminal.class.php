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
    * Classe de mapeamento da tabela TESOURARIA_USUARIO_TERMINAL
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
  * Efetua conexão com a tabela  TESOURARIA_USUARIO_TERMINAL
  * Data de Criação: 06/09/2005

  * @author Analista: Lucas Oiagen
  * @author Desenvolvedor: Cleisson da Silva Barboza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaUsuarioTerminal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaUsuarioTerminal()
{
    parent::Persistente();
    $this->setTabela("tesouraria.usuario_terminal");

    $this->setCampoCod('');
     $this->setComplementoChave('timestamp_usuario,timestamp_terminal,cod_terminal,cgm_usuario');

    $this->AddCampo('timestamp_usuario' , 'timestamp', true, '' , true , false );
    $this->AddCampo('timestamp_terminal', 'timestamp', true, '' , true , true  );
    $this->AddCampo('cgm_usuario'       , 'integer'  , true, '' , true , true  );
    $this->AddCampo('cod_terminal'      , 'integer'  , true, '' , true , true  );
    $this->AddCampo('responsavel'       , 'boolean'  , true, '' , false, false );
}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT                                                               \n";
    $stSql .= "    TUT.cod_terminal,                                                \n";
    $stSql .= "    TUT.timestamp_terminal,                                          \n";
    $stSql .= "    TUT.cgm_usuario,                                                 \n";
    $stSql .= "    TUT.timestamp_usuario,                                           \n";
    $stSql .= "    TUT.responsavel,                                                 \n";
    $stSql .= "    CGM.nom_cgm,                                                     \n";
    $stSql .= "    TT.cod_verificador                                              \n";
    $stSql .= "FROM                                                                 \n";
    $stSql .= "    tesouraria.terminal AS TT                                        \n";
    $stSql .= "        LEFT JOIN tesouraria.terminal_desativado AS TTD ON(          \n";
    $stSql .= "            TT.cod_terminal       = TTD.cod_terminal       AND       \n";
    $stSql .= "            TT.timestamp_terminal = TTD.timestamp_terminal           \n";
    $stSql .= "        ),                                                           \n";
    $stSql .= "    tesouraria.usuario_terminal AS TUT                               \n";
    $stSql .= "        LEFT JOIN tesouraria.usuario_terminal_excluido AS TUTE ON(   \n";
    $stSql .= "            TUT.cod_terminal       = TUTE.cod_terminal       AND     \n";
    $stSql .= "            TUT.timestamp_terminal = TUTE.timestamp_terminal AND     \n";
    $stSql .= "            TUT.cgm_usuario        = TUTE.cgm_usuario        AND     \n";
    $stSql .= "            TUT.timestamp_usuario  = TUTE.timestamp_usuario          \n";
    $stSql .= "        )                                                            \n";
    $stSql .= "    ,sw_cgm     AS CGM                                               \n";
    $stSql .= "WHERE                                                                \n";
    $stSql .= "    TUT.cod_terminal       = TT.cod_terminal       AND               \n";
    $stSql .= "    TUT.timestamp_terminal = TT.timestamp_terminal AND               \n";
    $stSql .= "                                                                     \n";
    $stSql .= "    TUT.cgm_usuario = CGM.numcgm                                     \n";

    return $stSql;
}

    function recuperaCodigoTimestamp(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCodigoTimestamp().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaCodigoTimestamp(){
	$stSql = " SELECT usuario_terminal.cod_terminal
                        , usuario_terminal.timestamp_terminal
                     FROM tesouraria.terminal
               
               INNER JOIN tesouraria.usuario_terminal 
                       ON usuario_terminal.cod_terminal       = terminal.cod_terminal
                      AND usuario_terminal.timestamp_terminal = terminal.timestamp_terminal
                    
                    WHERE usuario_terminal.timestamp_terminal = ( SELECT MAX(timestamp_terminal)
                                                                    FROM tesouraria.usuario_terminal
                                                                   WHERE cgm_usuario = ".$this->getDado('cgm_usuario')."
                                                                     AND responsavel = true )";
        
	return $stSql;
    }

}

?>