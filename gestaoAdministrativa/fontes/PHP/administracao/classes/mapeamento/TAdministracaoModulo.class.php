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
* Classe de mapeamento para administracao.modulo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27012 $
$Name$
$Author: vitorhugo $
$Date: 2007-12-03 16:48:58 -0200 (Seg, 03 Dez 2007) $

Casos de uso: uc-01.03.91
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TModulo extends Persistente
{
    public function TModulo()
    {
        parent::Persistente();
        $this->setTabela('administracao.modulo');
        $this->setCampoCod('cod_modulo');

        $this->AddCampo('cod_modulo',        'integer', true, '', true,  false            );
        $this->AddCampo('cod_gestao',        'integer', true, '', false, true            );
        $this->AddCampo('cod_responsavel',   'integer', true, '', false, true             );
        $this->AddCampo('nom_modulo',        'varchar', true, 40, false, false            );
        $this->AddCampo('nom_diretorio',     'varchar', true, 40, false, false            );
        $this->AddCampo('ordem',             'integer', true, '', false, false            );
    }

    public function recuperaListaModulos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaModulo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
     }

     function montaRecuperaListaModulo()
     {
         $stSql ="   SELECT                                     \n";
         $stSql.="      cod_modulo,                             \n";
         $stSql.="      cod_gestao,                             \n";
         $stSql.="      cod_responsavel,                        \n";
         $stSql.="      nom_modulo,                             \n";
         $stSql.="      nom_diretorio,                          \n";
         $stSql.="      ordem                                   \n";
         $stSql.="   FROM                                       \n";
         $stSql.="      administracao.modulo                    \n";

         return $stSql;
     }

}

//CLASSE CRIADA PARA MANTER A COMPATILIDADE COM O SISTEMA
//QUALQUER IMPLEMENTAÇÃO DEVER SER FEITA NA CLASSE TMODULO
class TAdministracaoModulo extends TModulo
{
    public function TAdministracaoModulo()
    {
        parent::TModulo();
    }
}
