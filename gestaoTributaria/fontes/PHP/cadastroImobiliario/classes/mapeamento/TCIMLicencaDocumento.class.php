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
     * Classe de mapeamento para a tabela IMOBILIARIO.licenca_documento
     * Data de Criação: 18/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: TCIMLicencaDocumento.class.php 59762 2014-09-09 21:02:35Z carolina $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMLicencaDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMLicencaDocumento()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.licenca_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca,exercicio');

        $this->AddCampo( 'cod_licenca', 'integer', true, '', true, true );
        $this->AddCampo( 'exercicio', 'varchar', true, '4', true, true );
        $this->AddCampo( 'cod_tipo_documento', 'integer', true, '', false, true );
        $this->AddCampo( 'cod_documento', 'integer', true, '', false, true );
        $this->AddCampo( 'num_documento', 'integer', true, '', false, false );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
    }

    public function recuperaNumeroDocumento(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNumeroDocumento().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeroDocumento()
    {
        ;
        //-------------
        $stSQL = " SELECT
                      max( num_documento ) AS num_documento
                   FROM
                      imobiliario.licenca_documento
                   WHERE
                      licenca_documento.exercicio = '".Sessao::getExercicio()."'";

        return $stSQL;
    }
}
