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
     * Classe de mapeamento para a tabela IMOBILIARIO.CORRETAGEM
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMCorretagem.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CORRETAGEM
  * Data de Criação: 18/01/2005

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMCorretagem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMCorretagem()
{
    parent::Persistente();
    $this->setTabela('imobiliario.corretagem');

    $this->setCampoCod('creci');
    $this->setComplementoChave('');

    $this->AddCampo('creci' ,'varchar',true,'10',true ,true);
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrder) {
        $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stFiltro.$stOrder;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                             \n";
    $stSql .= "    CR.CRECI,                      \n";
    $stSql .= "    CASE                           \n";
    $stSql .= "        WHEN                       \n";
    $stSql .= "            IM.CRECI IS NOT NULL   \n";
    $stSql .= "        THEN                       \n";
    $stSql .= "            IM.NUMCGM              \n";
    $stSql .= "        WHEN                       \n";
    $stSql .= "            COR.CRECI IS NOT NULL  \n";
    $stSql .= "        THEN                       \n";
    $stSql .= "            COR.NUMCGM             \n";
    $stSql .= "    END AS NUMCGM,                 \n";
    $stSql .= "    CGM.NOM_CGM                    \n";
    $stSql .= "FROM                               \n";
    $stSql .= "  imobiliario.corretagem AS CR         \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  imobiliario.imobiliaria AS IM        \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    CR.CRECI = IM.CRECI            \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  imobiliario.corretor AS COR          \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    CR.CRECI = COR.CRECI           \n";
    $stSql .= "LEFT JOIN                          \n";
    $stSql .= "  sw_cgm AS CGM                    \n";
    $stSql .= "ON                                 \n";
    $stSql .= "    IM.NUMCGM  = CGM.NUMCGM OR     \n";
    $stSql .= "    COR.NUMCGM = CGM.NUMCGM        \n";

    return $stSql;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  = "SELECT                                          \n";
    $stSql .= "    CR.CRECI,                                   \n";
    $stSql .= "    CASE                                        \n";
    $stSql .= "        WHEN                                    \n";
    $stSql .= "            IM.CRECI IS NOT NULL                \n";
    $stSql .= "        THEN                                    \n";
    $stSql .= "            IM.NUMCGM                           \n";
    $stSql .= "        WHEN                                    \n";
    $stSql .= "            COR.CRECI IS NOT NULL               \n";
    $stSql .= "        THEN                                    \n";
    $stSql .= "            COR.NUMCGM                          \n";
    $stSql .= "    END AS NUMCGM,                              \n";
    $stSql .= "    CASE                                        \n";
    $stSql .= "        WHEN                                    \n";
    $stSql .= "            IM.CRECI IS NOT NULL                \n";
    $stSql .= "        THEN                                    \n";
    $stSql .= "            'IMOBILIARIA'                       \n";
    $stSql .= "        WHEN                                    \n";
    $stSql .= "            COR.CRECI IS NOT NULL               \n";
    $stSql .= "        THEN                                    \n";
    $stSql .= "            'CORRETOR'                          \n";
    $stSql .= "    END AS TIPO_CORRETAGEM,                     \n";
    $stSql .= "    CGM.NOM_CGM,                                \n";
    $stSql .= "    CGM_RESP.NOM_CGM AS NOM_CGM_RESP,           \n";
    $stSql .= "    CGM_RESP.NUMCGM AS NUMCGM_RESP              \n";
    $stSql .= "FROM                                            \n";
    $stSql .= "  imobiliario.corretagem AS CR                      \n";
    $stSql .= "LEFT JOIN                                       \n";
    $stSql .= "  imobiliario.imobiliaria AS IM                     \n";
    $stSql .= "ON                                              \n";
    $stSql .= "    CR.CRECI = IM.CRECI                         \n";
    $stSql .= "LEFT JOIN                                       \n";
    $stSql .= "  imobiliario.corretor AS COR                       \n";
    $stSql .= "ON                                              \n";
    $stSql .= "    CR.CRECI = COR.CRECI                        \n";
    $stSql .= "LEFT JOIN                                       \n";
    $stSql .= "  sw_cgm AS CGM                                 \n";
    $stSql .= "ON                                              \n";
    $stSql .= "    IM.NUMCGM  = CGM.NUMCGM OR                  \n";
    $stSql .= "    COR.NUMCGM = CGM.NUMCGM                     \n";

    $stSql .= "LEFT JOIN                                       \n";
    $stSql .= "  imobiliario.corretor AS COR2                  \n";
    $stSql .= "ON                                              \n";
    $stSql .= "  IM.RESPONSAVEL = COR2.CRECI                   \n";

    $stSql .= "LEFT JOIN                                       \n";
    $stSql .= "  sw_cgm AS CGM_RESP                            \n";
    $stSql .= "ON                                              \n";
    $stSql .= "   COR2.numcgm  = CGM_RESP.NUMCGM               \n";

//    $stSql .= "LEFT JOIN                                       \n";
//    $stSql .= "  sw_cgm AS CGM_RESP                        \n";
//    $stSql .= "ON                                              \n";
//    $stSql .= "    IM.RESPONSAVEL  = CGM_RESP.NUMCGM           \n";

    $stSql .= "WHERE                                           \n";
    $stSql .= "    CGM.numcgm IS NOT NULL                      \n";

    return $stSql;
}

}
