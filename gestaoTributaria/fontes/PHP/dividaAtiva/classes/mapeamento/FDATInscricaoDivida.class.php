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
    * Classe de mapeamento da tabela DIVIDA.AUTORIDADE
    * Data de Criação: 14/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATAutoridade.class.php 29252 2008-04-16 14:25:51Z fabio $

* Casos de uso: uc-05.04.08
*/

/*
$Log$
Revision 1.3  2006/09/26 11:11:12  dibueno
adição de mais um campo no SQL de busca, concatenado o nom_cgm e numcgm no alias autoridade

Revision 1.2  2006/09/22 09:59:49  cercato
correcao do caso de uso.

Revision 1.1  2006/09/18 17:19:17  cercato
classes de mapeamento para as tabelas "autoridade" e "procurador".

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class FDATInscricaoDivida extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FDATInscricaoDivida()
    {
        parent::Persistente();
        $this->setTabela('divida.fn_inscricao_divida');
    }

    public function InscricaoDivida(&$rsRecordSet,$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $stSql  = $this->montaInscricaoDivida($stFiltro).$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaInscricaoDivida()
    {
        $stSql .= "SELECT ".$this->getTabela()."(".$this->getDado('lancamento').",".$this->getDado('exercicio').",".$this->getDado('modalidade').",".$this->getDado('autoridade').",".$this->getDado('usuario').",'".$this->getDado('dtinscricao')."'::date,".$this->getDado('processo').",".$this->getDado('exercicio_processo').") as inscricao";

        return $stSql;
    }

}// end of class

?>
