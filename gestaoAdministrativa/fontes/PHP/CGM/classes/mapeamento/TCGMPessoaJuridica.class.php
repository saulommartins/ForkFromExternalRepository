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
* Classe de Mapeamento para tabela cgm_pessoa_juridica
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 16517 $
$Name$
$Author: souzadl $
$Date: 2006-10-09 05:52:50 -0300 (Seg, 09 Out 2006) $

Casos de uso: uc-01.02.92, uc-01.02.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCGMPessoaJuridica extends Persistente
{
    public function TCGMPessoaJuridica()
    {
        parent::Persistente();
        $this->setTabela('sw_cgm_pessoa_juridica');
        $this->setCampoCod('numcgm');

        $this->AddCampo('numcgm',        'integer', true, '', true,  true);
        $this->AddCampo('cnpj',          'varchar', true, 14, false, false);
        $this->AddCampo('insc_estadual', 'varchar', true, 14, false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "SELECT                                         \n";
        $stSql .= "    CGM.*,                                     \n";
        $stSql .= "    PJ.*                                       \n";
        $stSql .= "FROM                                           \n";
        $stSql .= "    sw_cgm                  AS CGM,	          \n";
        $stSql .= "    ".$this->getTabela()."   AS PJ             \n";
        $stSql .= "        WHERE                                  \n";
        $stSql .= "            CGM.numcgm = PJ.numcgm             \n";
        $stSql .= "        AND CGM.numcgm <> 0                    \n";

        return $stSql;
    }

    public function recuperaDadosPessoaJuridica(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = $stOrdem ? $stOrdem : " ORDER BY sw_cgm.numcgm ";
        $stSql  = $this->montaRecuperaDadosPessoaJuridica().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosPessoaJuridica()
    {
        $stSql  = "SELECT sw_cgm_pessoa_juridica.numcgm                                                                                    \n";
        $stSql .= "     , sw_cgm.nom_cgm                                                                                                   \n";
        $stSql .= "     , sw_cgm_pessoa_juridica.cnpj                                                                                      \n";
        $stSql .= "     , sw_cgm.tipo_logradouro||' '||sw_cgm.logradouro||', '||sw_cgm.numero||' '||sw_cgm.complemento AS endereco         \n";
        $stSql .= "     , sw_cgm.bairro                                                                                                    \n";
        $stSql .= "     , sw_municipio.nom_municipio                                                                                       \n";
        $stSql .= "     , sw_cgm.fone_comercial                                                                                            \n";
        $stSql .= "  FROM sw_cgm_pessoa_juridica                                                                                           \n";
        $stSql .= "     , sw_cgm                                                                                                           \n";
        $stSql .= "     , sw_municipio                                                                                                     \n";
        $stSql .= " WHERE sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm                                                                    \n";
        $stSql .= "   AND sw_cgm.cod_uf = sw_municipio.cod_uf                                                                              \n";
        $stSql .= "   AND sw_cgm.cod_municipio = sw_municipio.cod_municipio                                                                \n";

        return $stSql;
    }
}
