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
 * Classe Visão Despesa/Receita
 *
 * @author Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 */

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';

class VLDODespesaReceita
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(RLDODespesaReceita $obModel)
    {
        $this->obModel= $obModel;
    }

    /**
     * Metodo para manter os dados arrecadado/liquidado
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param integer $inCodTipo
     * @param string  $stTipo
     * @param integer $inAno
     * @param integer $inColuna
     * @param float   $flValor
     *
     * @return void
     */
    public function manterArrecadadoLiquidado($inCodTipo,$stTipo,$inAno,$inColuna,$flValor,&$boTransacao = '')
    {   
        $this->obModel->inCodTipo                        = $inCodTipo;
        $this->obModel->stTipo                           = $stTipo;
        $this->obModel->stExercicio                      = $inAno - 5 + $inColuna;
        $this->obModel->flArrecadadoLiquidado            = $flValor;
        $this->obModel->flPrevistoFixado                 = null;
        $this->obModel->flProjetado                      = null;

        $obErro = $this->obModel->verificaDado($boExiste,$boTransacao);
        if($flValor){
            if ($boExiste) {
                $this->obModel->alterar($boTransacao);
            } else {
                $this->obModel->flPrevistoFixado             = 0;
                $this->obModel->flProjetado                  = 0;
                $this->obModel->incluir($boTransacao);
            }
        }
    }

    /**
     * Metodo para manter os dados previsto/fixado
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param integer $inCodTipo
     * @param string  $stTipo
     * @param integer $inAno
     * @param integer $inColuna
     * @param float   $flValor
     *
     * @return void
     */
    public function manterPrevistoFixado($inCodTipo,$stTipo,$inAno,$inColuna,$flValor,&$boTransacao = '')
    {
        $this->obModel->inCodTipo                        = $inCodTipo;
        $this->obModel->stTipo                           = $stTipo;
        $this->obModel->stExercicio                      = $inAno - 5 + $inColuna;
        $this->obModel->flArrecadadoLiquidado            = null;
        $this->obModel->flPrevistoFixado                 = $flValor;
        $this->obModel->flProjetado                      = null;

        $obErro = $this->obModel->verificaDado($boExiste,$boTransacao);
        if($flValor){
            if ($boExiste) {
                $this->obModel->alterar($boTransacao);
            } else {
                $this->obModel->flArrecadadoLiquidado        = 0;
                $this->obModel->flProjetado                  = 0;
                $this->obModel->incluir($boTransacao);
            }
        }
    }

    /**
     * Metodo para manter os dados projetado
     *
     * @author      Analista            Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor       Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param integer $inCodTipo
     * @param string  $stTipo
     * @param integer $inAno
     * @param integer $inColuna
     * @param float   $flValor
     *
     * @return void
     */
    public function manterProjetado($inCodTipo,$stTipo,$inAno,$inColuna,$flValor,&$boTransacao = '')
    {
        $this->obModel->inCodTipo                        = $inCodTipo;
        $this->obModel->stTipo                           = $stTipo;
        $this->obModel->stExercicio                      = $inAno -1 + $inColuna;
        $this->obModel->flArrecadadoLiquidado            = null;
        $this->obModel->flPrevistoFixado                 = null;
        $this->obModel->flProjetado                      = $flValor;

        $obErro = $this->obModel->verificaDado($boExiste,$boTransacao);
        if($flValor){
            if ($boExiste) {
                $this->obModel->alterar($boTransacao);
            } else {
                $this->obModel->flArrecadadoLiquidado        = 0;
                $this->obModel->flPrevistoFixado             = 0;
                $this->obModel->incluir($boTransacao);
            }
        }
    }

    /**
     * Metodo inclui
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam     array
     * @param object $boTransacao Transacao
     *
     * @return void
     */
    public function incluir(array $arParam)
    {
        $obErro = new Erro();
        
        //recupera o exercicio inicial do ppa
        $obTPPA = new TPPA;
        $obTPPA->recuperaTodos($rsPPA,' WHERE cod_ppa = ' . $arParam['inCodPPA']);
        $inAno = $arParam['inAno'] - $rsPPA->getCampo('ano_inicio') + 1;

        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $inAno;

        //verifica se o ldo ja esta cadastrado
        $this->obModel->obRLDOLDO->listar($rsLDO);
        if ($rsLDO->getNumLinhas() <= 0) {
            $this->obModel->obRLDOLDO->incluir($boTransacao);
        }

        foreach ($arParam as $stKey => $stValue) {
            if (strpos($stKey,'flValorAno') !== false) {
                $arInfo = explode('_',$stKey);
                if ($arInfo[4] == '1' AND $arInfo[5] == 0) {
                    switch ($arInfo[3]) {
                    case 'AL':
                        $this->manterArrecadadoLiquidado($arInfo[1],$arInfo[2],$arParam['inAno'],substr($arInfo[0],-1),$stValue,$boTransacao);
                        break;
                    case 'PF':
                        $this->manterPrevistoFixado($arInfo[1],$arInfo[2],$arParam['inAno'],substr($arInfo[0],-1),$stValue,$boTransacao);
                        break;
                    case 'PJ':
                        $this->manterProjetado($arInfo[1],$arInfo[2],$arParam['inAno'],substr($arInfo[0],-1),$stValue,$boTransacao);
                        break;
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLDespesaReceita.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Ano ' . $arParam['inAno'], 'incluir','aviso', Sessao::getId(), "../");
        } else {
            return sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo que monta a lista os valores
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function listValores(&$rsRecordSet,$arParam)
    {
        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $arParam['slExercicioLDO'];

        switch ($arParam['tipo']) {
        case 'receita_arrecadado':
            $obErro = $this->obModel->listReceitasLDO($rsRecordSet);
            break;
        case 'despesa_liquidado':
            $obErro = $this->obModel->listDespesasLDO($rsRecordSet);
            break;
        case 'receita_previsto':
            $obErro = $this->obModel->listReceitasPrevistasLDO($rsRecordSet);
            break;
        case 'despesa_fixado':
            $obErro = $this->obModel->listDespesasFixadasLDO($rsRecordSet);
            break;
        case 'receita_projetado':
            $obErro = $this->obModel->listReceitasProjetadasLDO($rsRecordSet);
            break;
        case 'despesa_projetado':
            $obErro = $this->obModel->listDespesasProjetadasLDO($rsRecordSet);
            break;
        }

        return $obErro;
    }

    /**
     * Metodo que preenche os dados do combo da LDO
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam array
     *
     * @return void
     */
    public function preencheLDO(array $arParam)
    {
        $stJs  = "jq('#slExercicioLDO').removeOption(/./);";
        $stJs .= "var arOptions = {";
        if ($arParam['inCodPPA'] != '') {
            $obTLDO = new TLDO;
            $obTLDO->setDado('cod_ppa',$arParam['inCodPPA']);
            $obTLDO->recuperaExerciciosLDO($rsLDO,' ORDER BY ano_ldo ');
            while (!$rsLDO->eof()) {
                $stJs .= "'" . $rsLDO->getCampo('ano_ldo') . "' : '" . $rsLDO->getCampo('ano_ldo') . "',";

                $rsLDO->proximo();
            }
        }
        $stJs .= "};";
        $stJs .= "jq('#slExercicioLDO').addOption(arOptions,false);";

        return $stJs;
    }

    /**
     * Metodo que cria as tabelas com os dados
     **/
    public function geraTabelas(&$obFormulario, $arParam, $stTipo)
    {
        switch ($stTipo) {
        case 'arrecadado':
            $stDescTable1 = 'Arrecadado';
            $stDescTable2 = 'Liquidado';
            $stTipoTable = 'AL';

            //recupera os dados da receita para a lista
            $arParam['tipo'] = 'receita_arrecadado';
            $this->listValores($rsReceitas, $arParam);

            //recupera os dados para a despesa
            $arParam['tipo'] = 'despesa_liquidado';
            $this->listValores($rsDespesas, $arParam);
            break;
        case 'previsto':
            $stDescTable1 = 'Previsto';
            $stDescTable2 = 'Fixado';
            $stTipoTable = 'PF';

            $arParam['tipo'] = 'receita_previsto';
            $this->listValores($rsReceitas, $arParam);

            //recupera os dados para a despesa
            $arParam['tipo'] = 'despesa_fixado';
            $this->listValores($rsDespesas, $arParam);
            break;
        case 'projetado':
            $stDescTable1 = $stDescTable2 = 'Projetado';
            $stTipoTable = 'PJ';

            $arParam['tipo'] = 'receita_projetado';
            $this->listValores($rsReceitas, $arParam);

            //recupera os dados para a despesa
            $arParam['tipo'] = 'despesa_projetado';
            $this->listValores($rsDespesas, $arParam);
            break;
        }
        //Instancia um span para as receitas
        $obSpnTableReceita = new Span();
        $obSpnTableReceita->setId('spnReceitas');

        //Instancia um span para despesas
        $obSpnTableDespesa = new Span();
        $obSpnTableDespesa->setId('spnDespesas');

        //Instancia um span para reserva
        $obSpnTableReserva = new Span();
        $obSpnTableReserva->setId('spnReserva');

        //adiciona a formatacao de moeda
        $rsReceitas->addFormatacao('valor_1','NUMERIC_BR');
        $rsReceitas->addFormatacao('valor_2','NUMERIC_BR');
        $rsReceitas->addFormatacao('valor_3','NUMERIC_BR');
        $rsReceitas->addFormatacao('valor_4','NUMERIC_BR');

        //adiciona o registro para o totalizador
        $rsReceitas->add(array('cod_tipo'       => '00',
                               'exercicio'      => '0000',
                               'cod_estrutural' => '',
                               'descricao'      => 'TOTAL DA RECEITA',
                               'tipo'           => 'R',
                               'nivel'          => '0',
                               'rpps'           => '0',
                               'orcamento_1'    => '0',
                               'orcamento_2'    => '0',
                               'orcamento_3'    => '0',
                               'orcamento_4'    => '0',
                               'valor_1'        => '',
                               'valor_2'        => '',
                               'valor_3'        => '',
                               'valor_4'        => ''));

        //adiciona a formatacao de moeda
        $rsDespesas->addFormatacao('valor_1','NUMERIC_BR');
        $rsDespesas->addFormatacao('valor_2','NUMERIC_BR');
        $rsDespesas->addFormatacao('valor_3','NUMERIC_BR');
        $rsDespesas->addFormatacao('valor_4','NUMERIC_BR');

        //adiciona o registro totalizador
        $rsDespesas->add(array('cod_tipo'       => '99',
                               'exercicio'      => '9999',
                               'cod_estrutural' => '',
                               'descricao'      => 'TOTAL DA DESPESA',
                               'tipo'           => 'D',
                               'nivel'          => '0',
                               'rpps'           => '0',
                               'valor_1'        => '',
                               'valor_2'        => '',
                               'valor_3'        => '',
                               'valor_4'        => ''));

        //cria um numerico para os 4 exercicios anteriores ao ldo
        $obValorAno1 = new Numerico;
        $obValorAno1->setId              ('flValorAno1_[cod_tipo]_[tipo]_' . $stTipoTable);
        $obValorAno1->setName            ('flValorAno1_[cod_tipo]_[tipo]_' . $stTipoTable . '_[nivel]_[orcamento_1]');
        $obValorAno1->setLabel           (true);
        $obValorAno1->setClass           ('valor');
        $obValorAno1->setValue           ('[valor_1]');
        $obValorAno1->setMaxLength       (12);
        $obValorAno1->setSize            (10);
        $obValorAno1->setNegativo        (false);
        $obValorAno1->obEvento->setOnBlur('recalcularValores(1,this.name);');

        $obValorAno2 = new Numerico;
        $obValorAno2->setId              ('flValorAno2_[cod_tipo]_[tipo]_' . $stTipoTable);
        $obValorAno2->setName            ('flValorAno2_[cod_tipo]_[tipo]_' . $stTipoTable . '_[nivel]_[orcamento_2]');
        $obValorAno2->setLabel           (true);
        $obValorAno2->setClass           ('valor');
        $obValorAno2->setValue           ('[valor_2]');
        $obValorAno2->setMaxLength       (12);
        $obValorAno2->setSize            (10);
        $obValorAno2->setNegativo        (false);
        $obValorAno2->obEvento->setOnBlur('recalcularValores(2,this.name);');

        $obValorAno3 = new Numerico;
        $obValorAno3->setId              ('flValorAno3_[cod_tipo]_[tipo]_' . $stTipoTable);
        $obValorAno3->setName            ('flValorAno3_[cod_tipo]_[tipo]_' . $stTipoTable . '_[nivel]_[orcamento_3]');
        $obValorAno3->setLabel           (true);
        $obValorAno3->setClass           ('valor');
        $obValorAno3->setValue           ('[valor_3]');
        $obValorAno3->setMaxLength       (12);
        $obValorAno3->setSize            (10);
        $obValorAno3->setNegativo        (false);
        $obValorAno3->obEvento->setOnBlur('recalcularValores(3,this.name);');

        $obValorAno4 = new Numerico;
        $obValorAno4->setId              ('flValorAno4_[cod_tipo]_[tipo]_' . $stTipoTable);
        $obValorAno4->setName            ('flValorAno4_[cod_tipo]_[tipo]_' . $stTipoTable . '_[nivel]_[orcamento_4]');
        $obValorAno4->setLabel           (true);
        $obValorAno4->setClass           ('valor');
        $obValorAno4->setValue           ('[valor_4]');
        $obValorAno4->setMaxLength       (12);
        $obValorAno4->setSize            (10);
        $obValorAno4->setNegativo        (false);
        $obValorAno4->obEvento->setOnBlur('recalcularValores(4,this.name);');

        //cria a tabela para as receitas
        $obTableReceitas = new Table;
        $obTableReceitas->setId         ('tableReceita' . $stDescTable1);
        $obTableReceitas->setTitle      ('Receitas');
        $obTableReceitas->setRecordset  ($rsReceitas);
        //$obTableReceitas->setConditional(true, "#efefef");

        $obTableReceitas->Head->addCabecalho('Código'   , 10);
        if ($stTipo != 'projetado') {
            $obTableReceitas->Head->addCabecalho('Descrição', 40);

            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . ($_REQUEST['slExercicioLDO'] - 4) ,10);
            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . ($_REQUEST['slExercicioLDO'] - 3), 10);
            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . ($_REQUEST['slExercicioLDO'] - 2), 10);
            $obTableReceitas->Head->addCabecalho('Reestimado ' . ($_REQUEST['slExercicioLDO'] - 1), 10);
        } else {
            $obTableReceitas->Head->addCabecalho('Descrição', 50);

            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . $_REQUEST['slExercicioLDO']       ,10);
            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . ($_REQUEST['slExercicioLDO'] + 1) ,10);
            $obTableReceitas->Head->addCabecalho($stDescTable1 . ' ' . ($_REQUEST['slExercicioLDO'] + 2), 10);
        }

        $obTableReceitas->Body->addCampo('[cod_estrutural]', 'E');
        $obTableReceitas->Body->addCampo('[descricao]', 'E');
        $obTableReceitas->Body->addCampo($obValorAno1, 'D');
        $obTableReceitas->Body->addCampo($obValorAno2, 'D');
        $obTableReceitas->Body->addCampo($obValorAno3, 'D');

        if ($stTipo != 'projetado') {
            $obTableReceitas->Body->addCampo($obValorAno4, 'D');
        }

        $obTableReceitas->montaHTML();

        $obSpnTableReceita->setValue($obTableReceitas->getHtml());

        //cria a tabela para as despesas
        $obTableDespesas = new Table;
        $obTableDespesas->setId         ('tableDespesa' . $stDescTable2);
        $obTableDespesas->setTitle      ('Despesas');
        $obTableDespesas->setRecordset  ($rsDespesas);
       // $obTableDespesas->setConditional(true, "#efefef");

        $obTableDespesas->Head->addCabecalho('Código'   , 10);

        if ($stTipo != 'projetado') {
            $obTableDespesas->Head->addCabecalho('Descrição', 40);

            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 4) ,10);
            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 3), 10);
            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 2), 10);
            $obTableDespesas->Head->addCabecalho('Reestimado ' . ($_REQUEST['slExercicioLDO'] - 1), 10);
        } else {
            $obTableDespesas->Head->addCabecalho('Descrição', 50);

            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . $_REQUEST['slExercicioLDO']       ,10);
            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] + 1) ,10);
            $obTableDespesas->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] + 2), 10);
        }

        $obTableDespesas->Body->addCampo('[cod_estrutural]', 'E');
        $obTableDespesas->Body->addCampo('[descricao]', 'E');
        $obTableDespesas->Body->addCampo($obValorAno1, 'D');
        $obTableDespesas->Body->addCampo($obValorAno2, 'D');
        $obTableDespesas->Body->addCampo($obValorAno3, 'D');
        if ($stTipo != 'projetado') {
            $obTableDespesas->Body->addCampo($obValorAno4, 'D');
        }

        $obTableDespesas->montaHTML();

        $obSpnTableDespesa->setValue($obTableDespesas->getHtml());

        //cria um recordSet para as reservas
        $rsReservas = new RecordSet();
        $rsReservas->add(array('cod_tipo'       => 'RES1',
                               'exercicio'      => '',
                               'cod_estrutural' => '9.0.00.00.00.00.00',
                               'descricao'      => 'RESERVA DE CONTIGÊNCIA',
                               'tipo'           => 'C',
                               'nivel'          => '0',
                               'rpps'           => '0',
                               'valor_1'        => '',
                               'valor_2'        => '',
                               'valor_3'        => '',
                               'valor_4'        => ''));
        $rsReservas->add(array('cod_tipo'       => 'RES2',
                               'exercicio'      => '',
                               'cod_estrutural' => '7.7.99.99.99.99.99',
                               'descricao'      => 'RESERVA DE CONTIGÊNCIA DO RPPS',
                               'tipo'           => 'C',
                               'nivel'          => '0',
                               'rpps'           => '0',
                               'valor_1'        => '',
                               'valor_2'        => '',
                               'valor_3'        => '',
                               'valor_4'        => ''));

        //cria um numerico para os 4 exercicios anteriores ao ldo
        $obValorAno1->setId              ('flValorAno1_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno1->setName            ('flValorAno1_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno1->setValue           ('0,00');
        $obValorAno1->setNegativo        (false);
        $obValorAno1->obEvento->setOnBlur('');

        $obValorAno2->setId              ('flValorAno2_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno2->setName            ('flValorAno2_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno2->setValue           ('0,00');
        $obValorAno2->setNegativo        (false);
        $obValorAno2->obEvento->setOnBlur('');

        $obValorAno3->setId              ('flValorAno3_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno3->setName            ('flValorAno3_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno3->setValue           ('0,00');
        $obValorAno3->setNegativo        (false);
        $obValorAno3->obEvento->setOnBlur('');

        $obValorAno4->setId              ('flValorAno4_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno4->setName            ('flValorAno4_[cod_tipo]_[tipo]_[nivel]_' . $stTipoTable);
        $obValorAno4->setValue           ('0,00');
        $obValorAno4->setNegativo        (false);
        $obValorAno4->obEvento->setOnBlur('');

        $obTableReserva = new Table;
        $obTableReserva->setId         ('tableReserva' . $stTipoTable);
        $obTableReserva->setTitle      ('Despesas');
        $obTableReserva->setRecordset  ($rsReservas);
       // $obTableReserva->setConditional(true, "#efefef");

        $obTableReserva->Head->addCabecalho('Código'   , 10);

        if ($stTipo == 'arrecadado') {
            $obTableReserva->Head->addCabecalho('Descrição', 70);

            $obTableReserva->Head->addCabecalho('Reestimado ' . ($_REQUEST['slExercicioLDO'] - 1), 10);
        } elseif ($stTipo == 'previsto') {
            $obTableReserva->Head->addCabecalho('Descrição', 40);

            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 4) ,10);
            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 3), 10);
            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] - 2), 10);
            $obTableReserva->Head->addCabecalho('Reestimado ' . ($_REQUEST['slExercicioLDO'] - 1), 10);
        } else {
            $obTableReserva->Head->addCabecalho('Descrição', 50);

            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' .  $_REQUEST['slExercicioLDO']      ,10);
            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] + 1) ,10);
            $obTableReserva->Head->addCabecalho($stDescTable2 . ' ' . ($_REQUEST['slExercicioLDO'] + 2), 10);
        }

        $obTableReserva->Body->addCampo('[cod_estrutural]', 'E');
        $obTableReserva->Body->addCampo('[descricao]', 'E');
        if ($stTipo == 'arrecadado') {
            $obTableReserva->Body->addCampo($obValorAno4, 'D');
        } elseif ($stTipo == 'previsto') {
            $obTableReserva->Body->addCampo($obValorAno1, 'D');
            $obTableReserva->Body->addCampo($obValorAno2, 'D');
            $obTableReserva->Body->addCampo($obValorAno3, 'D');
            $obTableReserva->Body->addCampo($obValorAno4, 'D');
        } else {
            $obTableReserva->Body->addCampo($obValorAno1, 'D');
            $obTableReserva->Body->addCampo($obValorAno2, 'D');
            $obTableReserva->Body->addCampo($obValorAno3, 'D');
        }

        $obTableReserva->montaHTML();

        $obSpnTableReserva->setValue($obTableReserva->getHtml());

        $obFormulario->addTitulo      ('Receitas');
        $obFormulario->addSpan        ($obSpnTableReceita);
        $obFormulario->addTitulo      ('Despesas');
        $obFormulario->addSpan        ($obSpnTableDespesa);
        $obFormulario->addTitulo      ('Reservas de Contigência');
        $obFormulario->addSpan        ($obSpnTableReserva);
    }
}

?>
