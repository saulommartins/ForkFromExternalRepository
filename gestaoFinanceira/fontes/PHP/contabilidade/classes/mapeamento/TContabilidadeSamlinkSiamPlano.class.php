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
/*
    * classe de Exportação Arquivos Exercício Anterior
    * Data de Criação   : 04/02/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_SIAM );

class TContabilidadeSamlinkSiamPlano extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeSamlinkSiamPlano()
{
    parent::Persistente();
    $this->setTabela( PLANO );

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('c01_anoexe'   , 'integer'  , 'true', '', false, false );
    $this->AddCampo('c01_estrut'   , 'character', 'true', '13', false, false );
    $this->AddCampo('c01_reduz'    , 'integer'  , 'true', '', false, false );
    $this->AddCampo('c01_digito'   , 'character', 'true', '1', false, false );
    $this->AddCampo('c01_descr'    , 'character', 'true', '40', false, false );
    $this->AddCampo('c01_dbabre'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_crabre'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db01'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_dbabre'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_crabre'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db01'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr01'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db02'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr02'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db03'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr03'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db04'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr04'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db05'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr05'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db06'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr06'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db07'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr07'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db08'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr08'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db09'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr09'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db10'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr10'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db11'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr11'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_db12'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_cr12'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('c01_class'  , 'character', 'true', '2', false, false );
    $this->AddCampo('c01_analitica'  , 'boolean', 'true', '', false, false );
    $this->AddCampo('c01_siscon'  , 'character', 'true', '1', false, false );
}

function montaRecuperaDadosExportacao()
{
    $stSQL  = "SELECT cod_conta,                                                  \n";
    $stSQL .= " to_char(mov_deb_1,'99999999999999.99') as mov_deb_1,              \n";
    $stSQL .= " to_char(mov_cre_1,'99999999999999.99') as mov_cre_1,              \n";
    $stSQL .= " to_char(mov_deb_2,'99999999999999.99') as mov_deb_2,              \n";
    $stSQL .= " to_char(mov_cre_2,'99999999999999.99') as mov_cre_2,              \n";
    $stSQL .= " to_char(mov_deb_3,'99999999999999.99') as mov_deb_3,              \n";
    $stSQL .= " to_char(mov_cre_3,'99999999999999.99') as mov_cre_3,              \n";
    $stSQL .= " to_char(mov_deb_4,'99999999999999.99') as mov_deb_4,              \n";
    $stSQL .= " to_char(mov_cre_4,'99999999999999.99') as mov_cre_4,              \n";
    $stSQL .= " to_char(mov_deb_5,'99999999999999.99') as mov_deb_5,              \n";
    $stSQL .= " to_char(mov_cre_5,'99999999999999.99') as mov_cre_5,              \n";
    $stSQL .= " to_char(mov_deb_6,'99999999999999.99') as mov_deb_6,              \n";
    $stSQL .= " to_char(mov_cre_6,'99999999999999.99') as mov_cre_6               \n";
    $stSQL .= "FROM DBLINK                                                          \n";
    $stSQL .= "   (                                                                 \n";
//  $stSQL .= "       samlink.fn_getSamLinkConnStr()                                \n";
    $stSQL .= "       'host=".$this->getDado("stHost")." port=".$this->getDado("stPorta")." dbname=".$this->getDado("stBanco")." user=".$this->getDado("stUsuario")." password=".$this->getDado("stUsuario")."' \n";
    $stSQL .= "       ,                                                             \n";
    $stSQL .= "       '                                                             \n";
    $stSQL .= " SELECT                                                              \n";
    $stSQL .= "     c01_estrut as cod_conta,                                        \n";
    $stSQL .= "     c01_db01+c01_db02 as mov_deb_1,                                 \n";
    $stSQL .= "     c01_cr01+c01_cr02 as mov_cre_1,                                 \n";
    $stSQL .= "     c01_db03+c01_db04 as mov_deb_2,                                 \n";
    $stSQL .= "     c01_cr03+c01_cr04 as mov_cre_2,                                 \n";
    $stSQL .= "     c01_db05+c01_db06 as mov_deb_3,                                 \n";
    $stSQL .= "     c01_cr05+c01_cr06 as mov_cre_3,                                 \n";
    $stSQL .= "     c01_db07+c01_db08 as mov_deb_4,                                 \n";
    $stSQL .= "     c01_cr07+c01_cr08 as mov_cre_4,                                 \n";
    $stSQL .= "     c01_db09+c01_db10 as mov_deb_5,                                 \n";
    $stSQL .= "     c01_cr09+c01_cr10 as mov_cre_5,                                 \n";
    $stSQL .= "     c01_db11+c01_db12 as mov_deb_6,                                 \n";
    $stSQL .= "     c01_cr11+c01_cr12 as mov_cre_6                                  \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     ".PLANO." as pL                                                 \n";
    $stSQL .= " WHERE                                                               \n";
    $stSQL .= "     pl.c01_anoexe = '(".$this->getDado("stExercicio")."-1)'         \n";
    $stSQL .= "'                                                                    \n";
    $stSQL .= "    ) as tabela(                                                     \n";
    $stSQL .= "                 cod_conta    character(13),                         \n";
    $stSQL .= "                 mov_deb_1    double precision,                      \n";
    $stSQL .= "                 mov_cre_1    double precision,                      \n";
    $stSQL .= "                 mov_deb_2    double precision,                      \n";
    $stSQL .= "                 mov_cre_2    double precision,                      \n";
    $stSQL .= "                 mov_deb_3    double precision,                      \n";
    $stSQL .= "                 mov_cre_3    double precision,                      \n";
    $stSQL .= "                 mov_deb_4    double precision,                      \n";
    $stSQL .= "                 mov_cre_4    double precision,                      \n";
    $stSQL .= "                 mov_deb_5    double precision,                      \n";
    $stSQL .= "                 mov_cre_5    double precision,                      \n";
    $stSQL .= "                 mov_deb_6    double precision,                      \n";
    $stSQL .= "                 mov_cre_6    double precision                       \n";
    $stSQL .= "               )                                                     \n";
    $stSQL .= " ORDER BY cod_conta                                                  \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no mï¿½todo
    * montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacaoExercicioAnterior(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
