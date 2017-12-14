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
    * Classe de regra de mapeamento para FISCALIZACAO.DOCUMENTO
    * Data de Criacao: 25/07/2007

        * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    *Casos de uso: uc-05.07.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CLA_PERSISTENTE);

class TFISVinculo extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/

    public function TFISVinculo()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.documento_atividade');

        $this->setComplementoChave('cod_atividade');
        $this->setComplementoChave('cod_documento');

        $this->AddCampo('cod_atividade', 'integer', true, '', true, true);
        $this->AddCampo('cod_documento', 'integer', true, '', true, true);

    }

    public function recuperarDocumento(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDocumento($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaDocumento($condicao)
    {
        $stSql = "    SELECT documento.cod_documento                                        \n";
        $stSql.= "         , documento.nom_documento                                        \n";
        $stSql.= "      FROM fiscalizacao.documento                                         \n";
        $stSql.= "INNER JOIN fiscalizacao.documento_atividade                               \n";
        $stSql.= "        ON documento.cod_documento = documento_atividade.cod_documento    \n";
        $stSql.= "       AND cod_atividade =". $condicao. "                                 \n";

        return $stSql;
    }
}// fecha classe de mapeamento
