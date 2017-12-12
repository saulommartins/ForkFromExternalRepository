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
     * Classe de mapeamento para a tabela IMOBILIARIO.EMISSAO_DOCUMENTO
     * Data de Criação: 18/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: TCIMEmissaoDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMEmissaoDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMEmissaoDocumento()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.emissao_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca,exercicio');

        $this->AddCampo( 'cod_licenca', 'integer', true, '', true, true );
        $this->AddCampo( 'exercicio', 'varchar', true, '4', true, true );
        $this->AddCampo( 'numcgm', 'integer', true, '', false, true );
        $this->AddCampo( 'dt_emissao', 'date', true, '', false, false );
        $this->AddCampo( 'timestamp', 'timestamp', true, '', true, false );
        $this->AddCampo( 'timestamp_emissao', 'timestamp', false, '', false, false );
    }

    public function recuperaUltimoRegistro(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimoRegistro().$stFiltro;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
    }

    public function montaRecuperaUltimoRegistro()
    {
        $stSql = "
                    SELECT  coalesce ( max(num_emissao), 0 ) as valor
                        FROM economico.emissao_documento
        ";

        return $stSql;
    }
}
