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
* Classe de mapeamento para administracao.acao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.03.91
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAdministracaoAcao extends Persistente
{
    public function TAdministracaoAcao()
    {
        parent::Persistente();
        $this->setTabela('administracao.acao');
        $this->setCampoCod('cod_acao');

        $this->AddCampo('cod_acao',           'integer', true, '', true,  false);
        $this->AddCampo('cod_funcionalidade', 'integer', true, '', false, true );
        $this->AddCampo('nom_acao',           'varchar', true, 40, false, false);
        $this->AddCampo('nom_arquivo',        'varchar', true, 40, false, false);
        $this->AddCampo('complemento_acao',   'varchar', true, 80, false, false);
        $this->AddCampo('parametro',          'varchar', true, 10, false, false);
        $this->AddCampo('ordem',              'integer', true, '', false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT                                               ";
        $stSQL .= "     A.cod_acao,                                      ";
        $stSQL .= "     A.nom_acao,                                      ";
        $stSQL .= "     A.nom_arquivo,                                   ";
        $stSQL .= "     A.parametro,                                     ";
        $stSQL .= "     A.ordem,                                         ";
        $stSQL .= "     F.cod_funcionalidade,                            ";
        $stSQL .= "     F.nom_funcionalidade,                            ";
        $stSQL .= "     F.nom_diretorio,                                 ";
        $stSQL .= "     F.ordem,                                         ";
        $stSQL .= "     M.cod_modulo,                                    ";
        $stSQL .= "     M.cod_responsavel,                               ";
        $stSQL .= "     M.nom_modulo,                                    ";
        $stSQL .= "     M.nom_diretorio,                                 ";
        $stSQL .= "     M.ordem,                                         ";
        $stSQL .= "     G.nom_gestao,                                    ";
        $stSQL .= "     U.numcgm,                                        ";
        $stSQL .= "     U.cod_orgao,                                     ";
        $stSQL .= "     U.dt_cadastro,                                   ";
        $stSQL .= "     U.username,                                      ";
        $stSQL .= "     U.password,                                      ";
        $stSQL .= "     U.status                                         ";
        $stSQL .= " FROM                                                 ";
        $stSQL .= "     administracao.acao             A,                          ";
        $stSQL .= "     administracao.funcionalidade   F,                          ";
        $stSQL .= "     administracao.modulo           M,                          ";
        $stSQL .= "     administracao.gestao           G,                          ";
        $stSQL .= "     administracao.usuario          U                           ";
        $stSQL .= " WHERE                                                ";
        $stSQL .= "     A.cod_funcionalidade = F.cod_funcionalidade  AND ";
        $stSQL .= "     F.cod_modulo         = M.cod_modulo          AND ";
        $stSQL .= "     G.cod_gestao         = M.cod_gestao          AND ";
        $stSQL .= "     M.cod_responsavel    = U.numcgm                  ";

        return $stSQL;
    }

    public function recuperaRelacionamentoSemUsuario(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoSemUsuario().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoSemUsuario()
    {
        $stSQL  = " SELECT                                               \n";
        $stSQL .= "     A.cod_acao,                                      \n";
        $stSQL .= "     A.nom_acao,                                      \n";
        $stSQL .= "     A.nom_arquivo,                                   \n";
        $stSQL .= "     A.parametro,                                     \n";
        $stSQL .= "     A.ordem,                                         \n";
        $stSQL .= "     F.cod_funcionalidade,                            \n";
        $stSQL .= "     F.nom_funcionalidade,                            \n";
        $stSQL .= "     F.nom_diretorio as dir_funcionalidade,           \n";
        $stSQL .= "     F.ordem,                                         \n";
        $stSQL .= "     M.cod_modulo,                                    \n";
        $stSQL .= "     M.cod_responsavel,                               \n";
        $stSQL .= "     M.nom_modulo,                                    \n";
        $stSQL .= "     M.nom_diretorio as dir_modulo,                   \n";
        $stSQL .= "     M.ordem                                          \n";
        $stSQL .= " FROM                                                 \n";
        $stSQL .= "     administracao.acao             A,                          \n";
        $stSQL .= "     administracao.funcionalidade   F,                          \n";
        $stSQL .= "     administracao.modulo           M                           \n";
        $stSQL .= " WHERE                                                \n";
        $stSQL .= "     A.cod_funcionalidade = F.cod_funcionalidade  AND \n";
        $stSQL .= "     F.cod_modulo         = M.cod_modulo              \n";

        return $stSQL;
    }

    public function recuperaCaminhoAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCaminhoAcao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCaminhoAcao()
    {
        $stSQL  = " SELECT                                               \n";
        $stSQL .= "     G.nom_gestao,                                    \n";
        $stSQL .= "     G.cod_gestao,                                    \n";
        $stSQL .= "     G.ordem,                                         \n";
        $stSQL .= "     M.cod_modulo,                                    \n";
        $stSQL .= "     M.cod_responsavel,                               \n";
        $stSQL .= "     M.nom_modulo,                                    \n";
        $stSQL .= "     M.ordem,                                         \n";
        $stSQL .= "     F.cod_funcionalidade,                            \n";
        $stSQL .= "     F.nom_funcionalidade,                            \n";
        $stSQL .= "     F.ordem,                                         \n";
        $stSQL .= "     A.cod_acao,                                      \n";
        $stSQL .= "     A.nom_acao,                                      \n";
        $stSQL .= "     A.parametro,                                     \n";
        $stSQL .= "     A.ordem,                                         \n";
        $stSQL .= "     G.nom_diretorio || M.nom_diretorio || F.nom_diretorio || A.nom_arquivo  as caminho \n";
        $stSQL .= " FROM                                                 \n";
        $stSQL .= "     administracao.acao             A,                \n";
        $stSQL .= "     administracao.funcionalidade   F,                \n";
        $stSQL .= "     administracao.modulo           M,                \n";
        $stSQL .= "     administracao.gestao           G                 \n";
        $stSQL .= " WHERE                                                \n";
        $stSQL .= "     A.cod_funcionalidade = F.cod_funcionalidade  AND \n";
        $stSQL .= "     F.cod_modulo         = M.cod_modulo          AND \n";
        $stSQL .= "     G.cod_gestao         = M.cod_gestao              \n";

        return $stSQL;
    }

    public function recuperaPermissao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPermissao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPermissao()
    {
        $stSQL  = " SELECT *
                      FROM administracao.acao
                      JOIN administracao.permissao
                        ON acao.cod_acao = permissao.cod_acao
                     WHERE permissao.numcgm = ".Sessao::read('numCgm')."
                       AND permissao.ano_exercicio = '".Sessao::getExercicio()."'
                       AND permissao.cod_acao = ".$this->getDado('cod_acao')."
                   ";

         return $stSQL;

    }

}
