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
    * Classe de mapeamento da tabela de Configuração
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19500 $
    $Name$
    $Autor:$
    $Date: 2007-01-22 10:54:22 -0200 (Seg, 22 Jan 2007) $

    * Casos de uso: uc-03.04.08

*/

/*
$Log$
Revision 1.1  2007/01/22 12:53:53  bruce
cadastro de responsáveis por entidade.

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

/**
  * Efetua conexão com a tabela  Administração.configuracao
  * Data de Criação: 30/06/2006

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasConfiguracaoEntidade extends TAdministracaoConfiguracao
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasConfiguracaoEntidade()
    {
        parent::TAdministracaoConfiguracao();

        $this->setTabela('administracao.configuracao_entidade');
        $this->setComplementoChave('cod_modulo,parametro,exercicio, cod_entidade');
        $this->AddCampo('cod_entidade', 'integer', true, '', false, false);

        $this->SetDado("exercicio",Sessao::getExercicio());
        $this->SetDado("cod_modulo",35);
    }

    public function recuperaResponsaveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaResponsaveis(). $stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaResponsaveis()
    {
        $stSql = "SELECT cod_modulo
                       , parametro
                       , valor
                       , exercicio
                       , cod_entidade
                       , sw_cgm.nom_cgm
                  FROM
                      administracao.configuracao_entidade
                  join sw_cgm
                    on ( configuracao_entidade.valor = '".sw_cgm.numcgm."' )

                  where configuracao_entidade.parametro = 'responsavel'
                ";

        return $stSql;
    }

    public function deletaResponsaveis()
    {
        $obErro     = new Erro;
        $obConexao  = new Conexao;
        $this->setDebug( 'exclusao' );

        $stSql = "DELETE FROM administracao.configuracao_entidade where cod_modulo = 35 and parametro = 'responsavel'";
        $obErro = $obConexao->executaDML( $stSql );

        return $obErro;
    }

}
