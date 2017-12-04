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
    * Classe de regra de relatório para Evento
    * Data de Criação:26/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Relatório

    * Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoTabelaIrrfEvento.class.php'                 );
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php'                   );
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoEvento.class.php'                                );

class RRelatorioIRRF extends PersistenteRelatorio
{
    /**
        * @var Object
        * @access Private
    */

    public $obRFolhaPagamentoFGTS;

    // variaveis usadas no filtro do relatório
    public $arContratos   = array(); // array de contratos usada para o filtro
    public $arLotacoes    = array(); // array de lotações usado no filtro
    public $arLocais      = array(); // array de locais usado no filtro
    public $stTipoCalculo = '';      // por enquando este parametro é só pra constar pois não será considerado pelo filtro
    public $stOrdenacao   = '';      // A para alfabética por nome N para ordenar por código?????????
    public $stDataFinal   = '';
    public $stCompetencia = '';
    public $obTabelaIRRF ;

    public function setCompetencia($stComp) { $this->stCompetencia = $stComp; }
    public function getCompetencia() { return  $this->stCompetencia;   }

    public function setTipoCalculo($valor) { $this->stTipoCalculo = $valor; }
    public function getTipoCalculo() { return $this->stTipoCalculo;   }

    public function setDataInicio($data) { $this->stDataInicio = $data; }
    public function getDataInicio() { return $this->stDataInicio;  }

    public function setDataFinal($data) { $this->stDataFinal = $data; }
    public function getDataFinal() { return $this->stDataFinal;  }

    public function setOrdenacao($ordem = '') { $this->stOrdenacao = $ordem ; }
    public function getOrdenacao($ordem = '') { return $this->stOrdenacao   ; }

    public function addLotacao($valor) { $this->arLotacoes[] = $valor;  }
    public function limpaLotacao($valor) { $this->arLotacoes[] = array(); }

    public function addLocal($local) { $this->arLocais[] = $local;  }
    public function limpaLocais() { $this->arLocais = array();   }

    public function addContrato($inContrato) { $this->arContratos[] = $inContrato; }
    public function limpaContratos() { $this->arContratos = array();               }

    public function RRelatorioIRRF()
    {
    $arFiltro = Sessao::read("filtroRelatorio");

    }//function RRelatorioFGTS() {

    public function montaFiltro()
    {
        $arFiltro = Sessao::read("filtroRelatorio");
        $stFiltro = '';
        $arCondicoes = array();
        $stCondicao = '';

        //// filtro por contratos
        if (count( $this->arContratos ) > 0 ) {
            $stCondicao = ' contrato.registro in ( ' ;
            foreach ($this->arContratos as $inContrato) {
                $stCondicao .= $inContrato .', '; // retirando a ultima virgula que só serve pra dar erro na consulta
            }
            $stCondicao[strlen($stCondicao) - 2 ] = ')';
            $arCondicoes[] = $stCondicao;
        }

        //// filtro por lotações
        if ( count ( $this->arLotacoes) > 0 ) {

            $stCondicao = "orgao.cod_orgao in (";
            foreach ($this->arLotacoes as $stLotacao) {
                $stCondicao .= "'". $stLotacao ."' , ";
            }
            $stCondicao[strlen($stCondicao) - 2 ] = ')';// retirando a ultima virgula que só serve pra dar erro na consulta
            $arCondicoes[] = $stCondicao;
        }

        //// filtro por locais
        if ( count ( $this->arLocais ) > 0 ) {
            $stCondicao = '';
            $stLocais   = '( ';

            foreach ($this->arLocais as $stLocal) {
               $stLocais .= $stLocal. ', ';
            }
            $stLocais[strlen($stLocais) - 2 ] = ')';

            $arCondicoes[] = "contrato_servidor.cod_contrato in ( select cod_contrato
                               from pessoal.contrato_servidor_local
                               inner join ( select cod_local, max(timestamp)
                                            from pessoal.contrato_servidor_local as lc
                                            where lc.cod_local in $stLocais
                                            group by cod_local ) as max_local
                                   on max_local.cod_local = contrato_servidor_local.cod_local)";
        }

        if ( $this->getCompetencia () ) {
            $arCondicoes[] = "  to_char( periodo_movimentacao.dt_final, 'mm/yyyy') = '".$this->getCompetencia()."'";
        }

        if (count( $arCondicoes )>0 ) {
           for ( $i = 0 ; $i < count( $arCondicoes ) ; $i++  ) {
                $stFiltro .= ' and '. $arCondicoes[$i];
           }
        }

        return $stFiltro;
    }

    public function geraRecordSet(&$arRelatorio)
    {
        $arFiltro = Sessao::read("filtroRelatorio");

        $RFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;

        $stFiltro = '';
        $this->obTabelaIRRF = new TFolhaPagamentoTabelaIrrfEvento;

        $stFiltro =    $this->montaFiltro();

        if ($this->stOrdenacao  == 'N') {
           $stOrder = ' order by contrato.cod_contrato';
        } else {
           $stOrder = ' order by  sw_cgm.nom_cgm ';
        }

        if ($this->getTipoCalculo() != "") {
            $this->obTabelaIRRF->setDado("cod_configuracao",$this->getTipoCalculo());
            //Férias, Décimo ou Rescisão
            if ( $this->getTipoCalculo() == 2 or $this->getTipoCalculo() == 3 or $this->getTipoCalculo() == 4 ) {
                $this->obTabelaIRRF->setDado("desdobramento",$arFiltro["stDesdobramento"]);
            }

        } else {
            $this->obTabelaIRRF->setDado("cod_configuracao",0);
            $this->obTabelaIRRF->setDado("cod_complementar",$arFiltro["inCodComplementar"]);
        }

        $obErro =  $this->obTabelaIRRF->recuperaRelatorioIRRF ( $rsRelatorio , $stFiltro , $stOrder );

        // pegando competencia e periodo
        $stCompetencia = explode('/', $this->getCompetencia() );
        $stCompetencia = $stCompetencia[1] . '-' . $stCompetencia[0];
        $RFolhaPagamentoPeriodoMovimentacao->setDtFinal( $stCompetencia );
        $RFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsMovimentacao);

        /// pegando o tipo de Folha
        if ($this->getTipoCalculo() > 0) {
            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
            $obRFolhaPagamentoEvento->addConfiguracaoEvento();
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao       ( $this->getTipoCalculo() );
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento ( $rsConfiguracaoEvento   );
            $stTipoFolha = trim($rsConfiguracaoEvento->getCampo( 'descricao' ));
            switch ($this->getTipoCalculo()) {
                case 2:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'A':
                            $stTipoFolha .= " Desd.: Abono";
                            break;
                        case 'F':
                            $stTipoFolha .= " Desd.: Férias";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: Adiantamento";
                            break;
                    }
                    break;
                case 3:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'A':
                            $stTipoFolha .= " Desd.: Adiantamento 13º";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: Saldo 13º Salário";
                            break;
                        case 'C':
                            $stTipoFolha .= " Desd.: Complementação 13º";
                            break;
                    }
                    break;
                case 4:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'S':
                            $stTipoFolha .= " Desd.: Saldo Salário";
                            break;
                        case 'A':
                            $stTipoFolha .= " Desd.: Aviso Prévio";
                            break;
                        case 'V':
                            $stTipoFolha .= " Desd.: Férias Vencidas";
                            break;
                        case 'P':
                            $stTipoFolha .= " Desd.: Férias Proporcionais";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: 13º Salário";
                            break;
                    }
                    break;
            }
        } else {
            $stTipoFolha = "Complementar ". $arFiltro["inCodComplementar"];
        }
        $arTemp = array();
        $arTemp['campo1'] = "Tipo da Folha:";
        $arTemp['campo2'] = $stTipoFolha;
        $arTemp['campo3'] = "Competência:";
        $arTemp['campo4'] = $this->getCompetencia();
        $arTemp['campo5'] = "Período Movimentação:  ";
        $arTemp['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');
        $arRelatorio['linha1'][] = $arTemp;

        $arRelatorio['corpo'] = $rsRelatorio->getElementos();

        // gerando a linha de totais do relatório
        $flTotalBase     = 0;
        $flTotalDesconto = 0;
        $rsRelatorio->setPrimeiroElemento();
        while ( !$rsRelatorio->eof() ) {
           $flTotalBase     = $flTotalBase     + $rsRelatorio->getCampo('campo4');
           $flTotalDesconto = $flTotalDesconto + $rsRelatorio->getCampo('campo5');
           $rsRelatorio->proximo();
        }

        $arTemp['campo1'] = 'Nro. Servidores:' ;
        $arTemp['campo2'] = ($rsRelatorio->getNumLinhas() == -1) ? 0 : $rsRelatorio->getNumLinhas() ;
        $arTemp['campo3'] = 'TOTAIS';
        $arTemp['campo4'] = $flTotalBase;
        $arTemp['campo5'] = $flTotalDesconto;
        $arRelatorio['totais'][] = $arTemp;

        return $obErro;

    }//function geraRecordSet

    public function montaFiltroLotacao($inCont)
    {
        $arFiltro = Sessao::read("filtroRelatorio");

        $stCondicao = '';
        //// filtro por lotação
        if ( count ( $this->arLotacoes) > 0 ) {
            $stCondicao = " AND orgao.cod_orgao  = ".$this->arLotacoes[$inCont];
        }

        if ( $this->getCompetencia () ) {
            $stCondicao .= " AND to_char( periodo_movimentacao.dt_final, 'mm/yyyy') = '".$this->getCompetencia()."'";
        }

        return $stCondicao;
    }

    public function montaFiltroLocal($inCont)
    {
        $arFiltro = Sessao::read("filtroRelatorio");

        $stCondicao = '';
        //// filtro por locais
        if ( count ( $this->arLocais ) > 0 ) {

           $stCondicao = " AND EXISTS ( select max_local.cod_contrato
                               from pessoal.contrato_servidor_local csl
                               inner join ( select cod_local, cod_contrato, max(timestamp)
                                            from pessoal.contrato_servidor_local as lc
                                            where lc.cod_local = ".$this->arLocais[$inCont]."
                                            AND lc.cod_contrato = contrato_servidor.cod_contrato group by cod_local, cod_contrato ) as max_local
                                   on max_local.cod_local = csl.cod_local AND max_local.cod_contrato = csl.cod_contrato)";
        }

        if ( $this->getCompetencia () ) {
            $stCondicao .= " AND to_char( periodo_movimentacao.dt_final, 'mm/yyyy') = '".$this->getCompetencia()."'";
        }

        return $stCondicao;
    }

    public function geraRecordSetAgrupado(&$arRelatorio)
    {
        $arFiltro = Sessao::read("filtroRelatorio");

        $RFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;

        $stFiltro = '';
        $this->obTabelaIRRF = new TFolhaPagamentoTabelaIrrfEvento;

        if ($this->stOrdenacao  == 'N') {
           $stOrder = ' order by contrato.cod_contrato';
        } else {
           $stOrder = ' order by  sw_cgm.nom_cgm ';
        }

        if ($this->getTipoCalculo() != "") {
            $this->obTabelaIRRF->setDado("cod_configuracao",$this->getTipoCalculo());
            //Férias, Décimo ou Rescisão
            if ( $this->getTipoCalculo() == 2 or $this->getTipoCalculo() == 3 or $this->getTipoCalculo() == 4 ) {
                $this->obTabelaIRRF->setDado("desdobramento",$arFiltro["stDesdobramento"]);
            }
        } else {
            $this->obTabelaIRRF->setDado("cod_configuracao",0);
            $this->obTabelaIRRF->setDado("cod_complementar",$arFiltro["inCodComplementar"]);
        }

        // pegando competencia e periodo
        $stCompetencia = explode('/', $this->getCompetencia() );
        $stCompetencia = $stCompetencia[1] . '-' . $stCompetencia[0];
        $RFolhaPagamentoPeriodoMovimentacao->setDtFinal( $stCompetencia );
        $RFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsMovimentacao);

        /// pegando o tipo de Folha
        if ($this->getTipoCalculo() > 0) {
            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
            $obRFolhaPagamentoEvento->addConfiguracaoEvento();
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao       ( $this->getTipoCalculo() );
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento ( $rsConfiguracaoEvento   );
            $stTipoFolha = trim($rsConfiguracaoEvento->getCampo( 'descricao' ));
            switch ($this->getTipoCalculo()) {
                case 2:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'A':
                            $stTipoFolha .= " Desd.: Abono";
                            break;
                        case 'F':
                            $stTipoFolha .= " Desd.: Férias";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: Adiantamento";
                            break;
                    }
                    break;
                case 3:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'A':
                            $stTipoFolha .= " Desd.: Adiantamento 13º";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: Saldo 13º Salário";
                            break;
                        case 'C':
                            $stTipoFolha .= " Desd.: Complementação 13º";
                            break;
                    }
                    break;
                case 4:
                    switch ($arFiltro["stDesdobramento"]) {
                        case 'S':
                            $stTipoFolha .= " Desd.: Saldo Salário";
                            break;
                        case 'A':
                            $stTipoFolha .= " Desd.: Aviso Prévio";
                            break;
                        case 'V':
                            $stTipoFolha .= " Desd.: Férias Vencidas";
                            break;
                        case 'P':
                            $stTipoFolha .= " Desd.: Férias Proporcionais";
                            break;
                        case 'D':
                            $stTipoFolha .= " Desd.: 13º Salário";
                            break;
                    }
                    break;
            }
        } else {
            $stTipoFolha = "Complementar ". $arFiltro["inCodComplementar"];
        }

        $arTempCabecalho['campo1'] = "Tipo da Folha:";
        $arTempCabecalho['campo2'] = $stTipoFolha;
        $arTempCabecalho['campo3'] = "Competência:";
        $arTempCabecalho['campo4'] = $this->getCompetencia();
        $arTempCabecalho['campo5'] = "Período Movimentação:  ";
        $arTempCabecalho['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');

        if ( count($this->arLocais) > 0 ) {

                for ( $i = 0; $i < count($this->arLocais); $i++ ) {

                        $stFiltro =  $this->montaFiltroLocal( $i );
                        $obErro = $this->obTabelaIRRF->recuperaRelatorioIRRF ( $rsRelatorio , $stFiltro , $stOrder );

                        if ( $rsRelatorio->getNumLinhas() != -1 ) {

                                // gerando a linha de totais do relatório
                                $flTotalBase     = 0;
                                $flTotalDesconto = 0;
                                $rsRelatorio->setPrimeiroElemento();
                                while ( !$rsRelatorio->eof() ) {
                                   $flTotalBase     = $flTotalBase     + $rsRelatorio->getCampo('campo4');
                                   $flTotalDesconto = $flTotalDesconto + $rsRelatorio->getCampo('campo5');

                                       $arTempTotal['campo1'] = 'Nro. Servidores:' ;
                                       $arTempTotal['campo2'] = ($rsRelatorio->getNumLinhas() == -1) ? 0 : $rsRelatorio->getNumLinhas() ;
                                       $arTempTotal['campo3'] = 'TOTAIS';
                                       $arTempTotal['campo4'] = $flTotalBase;
                                       $arTempTotal['campo5'] = $flTotalDesconto;
                                           $arTempDados['corpo'] = $rsRelatorio->getElementos();
                                           $rsRelatorio->proximo();
                                }
                                $stFiltroOrganograma = ' WHERE cod_local = '.$this->arLocais[$i];
                                $obTOrganograma = new TOrganogramaLocal;
                                        $obTOrganograma->recuperaTodos( $rsOrganogramaLocal, $stFiltroOrganograma );

                                        $arTempFiltro['tipo_filtro'] = "Local: ".$rsOrganogramaLocal->getCampo("descricao");

                                $arTempDados['filtro'][0] = $arTempFiltro;
                                $arTempDados['linha1'][0] = $arTempCabecalho;
                                $arTempDados['totais'][0] = $arTempTotal;
                                $arRelatorio['agrupado'][] = $arTempDados;

                        }
                }
        }

        if ( count($this->arLotacoes) > 0 ) {

                for ( $i = 0; $i < count($this->arLotacoes); $i++ ) {

                        $stFiltro =    $this->montaFiltroLotacao( $i );
                        $obErro =  $this->obTabelaIRRF->recuperaRelatorioIRRF ( $rsRelatorio , $stFiltro , $stOrder );

                                if ( $rsRelatorio->getNumLinhas() != -1 ) {

                                // gerando a linha de totais do relatório
                                $flTotalBase     = 0;
                                $flTotalDesconto = 0;

                                $rsRelatorio->setPrimeiroElemento();
                                while ( !$rsRelatorio->eof() ) {
                                   $flTotalBase     = $flTotalBase     + $rsRelatorio->getCampo('campo4');
                                   $flTotalDesconto = $flTotalDesconto + $rsRelatorio->getCampo('campo5');

                                       $arTempTotal['campo1'] = 'Nro. Servidores:' ;
                                       $arTempTotal['campo2'] = ($rsRelatorio->getNumLinhas() == -1) ? 0 : $rsRelatorio->getNumLinhas() ;
                                       $arTempTotal['campo3'] = 'TOTAIS';
                                       $arTempTotal['campo4'] = $flTotalBase;
                                   $arTempTotal['campo5'] = $flTotalDesconto;
                                           $arTempDados['corpo'] = $rsRelatorio->getElementos();
                                           $arTempFiltro['tipo_filtro'] = "Lotação: ".$rsRelatorio->getCampo("lotacao");
                                           $rsRelatorio->proximo();
                                }

                                $arTempDados['filtro'][0] = $arTempFiltro;
                                $arTempDados['linha1'][0] = $arTempCabecalho;
                                $arTempDados['totais'][0] = $arTempTotal;
                                $arRelatorio['agrupado'][] = $arTempDados;

                                }

                }

        }

        return $obErro;
    }

}
